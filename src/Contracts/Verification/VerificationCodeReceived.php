<?php

declare(strict_types = 1);

namespace App\Contracts\Verification;

/**
 * @api
 *
 * @see IncorrectVerificationCodeReceived
 *
 * @property-read string $correlationId
 * @property-read string $confirmationCode
 */
final class VerificationCodeReceived
{
    /**
     * @var string
     */
    public $correlationId;

    /**
     * @var string
     */
    public $confirmationCode;

    /**
     * @param string $correlationId
     * @param string $confirmationCode
     */
    public function __construct(string $correlationId, string $confirmationCode)
    {
        $this->correlationId    = $correlationId;
        $this->confirmationCode = $confirmationCode;
    }
}