<?php

declare(strict_types = 1);

namespace App;

use function Amp\call;
use Amp\Promise;
use Amp\Success;
use App\Contracts\Verification\SendVerificationCode;
use App\Contracts\Verification\SendVerificationCodeFailed;
use App\Contracts\Verification\VerificationCodeSent;
use ServiceBus\Context\KernelContext;
use ServiceBus\Services\Annotations\CommandHandler;

final class VerificationService
{
    /**
     * @CommandHandler()
     *
     * @param SendVerificationCode $command
     * @param KernelContext        $context
     *
     * @return Promise
     */
    public function sendCode(SendVerificationCode $command, KernelContext $context): Promise
    {
        return call(
            static function(SendVerificationCode $command) use ($context): \Generator
            {
                try
                {
                    /** Some send service */
                    yield new Success();

                    $context->logContextMessage(
                        'Verification code: "{verificationCode}"',
                        ['verificationCode' => $command->confirmationCode]
                    );

                    yield $context->delivery(
                        new VerificationCodeSent($command->correlationId)
                    );
                }
                catch(\Throwable $throwable)
                {
                    yield $context->delivery(
                        new SendVerificationCodeFailed($command->correlationId, $throwable->getMessage())
                    );
                }
            },
            $command
        );
    }
}