<?php

declare(strict_types = 1);

namespace App\Contracts\Verification;

/**
 * @internal
 *
 * @see VerificationCodeSent
 * @see SendVerificationCodeFailed
 *
 * @property-read string $correlationId
 * @property-read string $customerId
 * @property-read string $confirmationCode
 */
final class SendVerificationCode
{
    /**
     * @var string
     */
    public $correlationId;

    /**
     * @var string
     */
    public $customerId;

    /**
     * @var string
     */
    public $confirmationCode;

    /**
     * @param string $correlationId
     * @param string $customerId
     * @param string $confirmationCode
     */
    public function __construct(string $correlationId, string $customerId, string $confirmationCode)
    {
        $this->correlationId    = $correlationId;
        $this->customerId       = $customerId;
        $this->confirmationCode = $confirmationCode;
    }
}