<?php

declare(strict_types = 1);

namespace App;

use function Amp\call;
use Amp\Promise;
use App\Contracts\ActivateCustomer\ActivateCustomer;
use App\Contracts\ActivateCustomer\ActivateCustomerFailed;
use App\Contracts\ActivateCustomer\CustomerActivated;
use App\Contracts\Register\RegisterCustomer;
use App\Contracts\Register\RegisterCustomerValidationFailed;
use App\Contracts\SaveCustomer\CustomerStored;
use App\Contracts\SaveCustomer\SaveCustomer;
use App\Contracts\SaveCustomer\SaveCustomerFailed;
use function ServiceBus\Common\uuid;
use ServiceBus\Context\KernelContext;
use ServiceBus\Sagas\Module\SagasProvider;
use ServiceBus\Services\Annotations\CommandHandler;
use ServiceBus\Storage\Common\DatabaseAdapter;
use ServiceBus\Storage\Common\Exceptions\UniqueConstraintViolationCheckFailed;
use function ServiceBus\Storage\Sql\equalsCriteria;
use function ServiceBus\Storage\Sql\updateQuery;

final class RegistrationService
{
    /**
     * Start registration process
     *
     * @CommandHandler(
     *     validate=true
     * )
     *
     * @param RegisterCustomer $command
     * @param KernelContext    $context
     * @param SagasProvider    $sagasProvider
     *
     * @return Promise
     */
    public function handle(RegisterCustomer $command, KernelContext $context, SagasProvider $sagasProvider): Promise
    {
        if(true === $context->isValid())
        {
            return $sagasProvider->start(
                new RegistrationSagaId($command->requestId, RegistrationSaga::class),
                $command,
                $context
            );
        }

        return $context->delivery(new RegisterCustomerValidationFailed($command->requestId, $context->violations()));
    }

    /**
     * Save customer to database
     *
     * @CommandHandler()
     *
     * @param SaveCustomer    $command
     * @param KernelContext   $context
     * @param DatabaseAdapter $databaseAdapter
     *
     * @return Promise
     */
    public function save(SaveCustomer $command, KernelContext $context, DatabaseAdapter $databaseAdapter): Promise
    {
        return call(
            static function(SaveCustomer $command) use ($context, $databaseAdapter): \Generator
            {
                try
                {
                    $id = uuid();

                    yield $databaseAdapter->execute(
                        'INSERT INTO customers (id, email, login, password, created_at) VALUES (?, ?, ?, ?, ?)', [
                            $id,
                            $command->email,
                            $command->login,
                            $command->password,
                            \date('Y-m-d H:i:s')
                        ]
                    );

                    yield $context->delivery(new CustomerStored($command->correlationId, $id));
                }
                catch(UniqueConstraintViolationCheckFailed $exception)
                {
                    yield $context->delivery(
                        new RegisterCustomerValidationFailed(
                            $command->correlationId,
                            ['email' => 'Duplicate email address']
                        )
                    );
                }
                catch(\Throwable $throwable)
                {
                    $context->logContextThrowable($throwable);

                    yield $context->delivery(new SaveCustomerFailed($command->correlationId, $throwable->getMessage()));
                }
            },
            $command
        );
    }

    /**
     * Activate customer
     *
     * @CommandHandler()
     *
     * @param ActivateCustomer $command
     * @param KernelContext    $context
     * @param DatabaseAdapter  $databaseAdapter
     *
     * @return Promise
     */
    public function activate(ActivateCustomer $command, KernelContext $context, DatabaseAdapter $databaseAdapter): Promise
    {
        return call(
            static function(ActivateCustomer $command) use ($context, $databaseAdapter): \Generator
            {
                try
                {
                    $query = updateQuery('customers', ['confirmed_at' => \date('Y-m-d H:i:s')])
                        ->where(equalsCriteria('id', $command->customerId))
                        ->compile();

                    yield $databaseAdapter->execute($query->sql(), $query->params());

                    yield $context->delivery(
                        new CustomerActivated($command->correlationId)
                    );
                }
                catch(\Throwable $throwable)
                {
                    yield $context->delivery(
                        new ActivateCustomerFailed($command->correlationId, $throwable->getMessage())
                    );
                }
            },
            $command
        );
    }
}
