<?php

declare(strict_types = 1);

namespace App;

use App\Contracts\ActivateCustomer\ActivateCustomer;
use App\Contracts\ActivateCustomer\ActivateCustomerFailed;
use App\Contracts\ActivateCustomer\CustomerActivated;
use App\Contracts\Register\CustomerRegistered;
use App\Contracts\Register\CustomerRegistrationFailed;
use App\Contracts\Register\RegisterCustomer;
use App\Contracts\Register\RegisterCustomerValidationFailed;
use App\Contracts\SaveCustomer\CustomerStored;
use App\Contracts\SaveCustomer\SaveCustomer;
use App\Contracts\SaveCustomer\SaveCustomerFailed;
use App\Contracts\Verification\VerificationCodeReceived;
use App\Contracts\Verification\IncorrectVerificationCodeReceived;
use App\Contracts\Verification\SendVerificationCode;
use App\Contracts\Verification\SendVerificationCodeFailed;
use function ServiceBus\Common\uuid;
use ServiceBus\Sagas\Configuration\Annotations\SagaEventListener;
use ServiceBus\Sagas\Configuration\Annotations\SagaHeader;
use ServiceBus\Sagas\Saga;

/**
 * @SagaHeader(
 *     idClass="App\RegistrationSagaId",
 *     containingIdProperty="correlationId"
 * )
 */
final class RegistrationSaga extends Saga
{
    private const MAX_RETRIES = 10;

    /**
     * @var string|null
     */
    private $customerId;

    /**
     * @var string|null
     */
    private $confirmationCode;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $retriesCount = 0;

    /**
     * @inheritDoc
     *
     * @param object $command
     *
     * @return void
     */
    public function start(object $command): void
    {
        /** @var RegisterCustomer $command */

        $this->email    = $command->email;
        $this->login    = $command->login;
        $this->password = \password_hash($command->clearPassword, \PASSWORD_DEFAULT);

        $this->fire(
            new SaveCustomer($this->id()->toString(), $this->email, $this->login, $this->password)
        );
    }

    /**
     * Some validation error
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @SagaEventListener()
     *
     * @param RegisterCustomerValidationFailed $event
     *
     * @return void
     */
    private function onRegisterCustomerValidationFailed(RegisterCustomerValidationFailed $event): void
    {
        $this->makeFailed('Validation error');
    }

    /**
     * Unknown error occurred
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @SagaEventListener()
     *
     * @param SaveCustomerFailed $event
     *
     * @return void
     */
    private function onSaveCustomerFailed(SaveCustomerFailed $event): void
    {
        if(self::MAX_RETRIES <= $this->retriesCount)
        {
            $this->fire(
                new SaveCustomer($event->correlationId, $this->email, $this->login, $this->password)
            );

            $this->retriesCount++;

            return;
        }

        $this->raise(
            new CustomerRegistrationFailed($event->correlationId, $event->reason)
        );
    }

    /**
     * Customer info successful stored in database
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @SagaEventListener()
     *
     * @param CustomerStored $event
     *
     * @return void
     */
    private function onCustomerStored(CustomerStored $event): void
    {
        $this->customerId       = $event->customerId;
        $this->confirmationCode = \sha1(uuid());

        $this->fire(
            new SendVerificationCode($event->correlationId, $this->customerId, $this->confirmationCode)
        );
    }

    /**
     * Send customer verification code failed
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @SagaEventListener()
     *
     * @param SendVerificationCodeFailed $event
     *
     * @return void
     */
    private function onSendVerificationCodeFailed(SendVerificationCodeFailed $event): void
    {
        /** retry? */

        $this->raise(
            new CustomerRegistrationFailed($event->correlationId, $event->reason)
        );
    }

    /**
     * Confirmation code received
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @SagaEventListener()
     *
     * @param VerificationCodeReceived $event
     *
     * @return void
     */
    private function onVerificationCodeReceived(VerificationCodeReceived $event): void
    {
        if($this->confirmationCode === $event->confirmationCode)
        {
            $this->fire(
                new ActivateCustomer($event->correlationId, $this->customerId)
            );

            return;
        }

        $this->raise(
            new IncorrectVerificationCodeReceived($event->correlationId)
        );
    }

    /**
     * Customer activated
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @SagaEventListener()
     *
     * @param CustomerActivated $event
     *
     * @return void
     */
    private function onCustomerActivated(CustomerActivated $event): void
    {
        $this->raise(
            new CustomerRegistered($event->correlationId, $this->customerId)
        );
    }

    /**
     * Customer activation failed
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @SagaEventListener()
     *
     * @param ActivateCustomerFailed $event
     *
     * @return void
     */
    private function onActivateCustomerFailed(ActivateCustomerFailed $event): void
    {
        /** retry? */

        $this->raise(
            new CustomerRegistrationFailed($event->correlationId, $event->reason)
        );
    }

    /**
     * Customer successful registered
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @SagaEventListener()
     *
     * @param CustomerRegistered $event
     *
     * @return void
     */
    private function onCustomerRegistered(CustomerRegistered $event): void
    {
        $this->makeCompleted();
    }

    /**
     * Some registration process error
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @SagaEventListener()
     *
     * @param CustomerRegistrationFailed $event
     *
     * @return void
     */
    private function onCustomerRegistrationFailed(CustomerRegistrationFailed $event): void
    {
        /** Any compensatory actions */

        $this->makeFailed($event->reason);
    }
}