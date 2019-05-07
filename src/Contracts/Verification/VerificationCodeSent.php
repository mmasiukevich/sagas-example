<?php

declare(strict_types = 1);

namespace App\Contracts\Verification;

/**
 * @internal
 *
 * @see SendVerificationCode
 *
 * @property-read string $correlationId
 */
final class VerificationCodeSent
{
    /**
     * @var string
     */
    public $correlationId;

    /**
     * @param string $correlationId
     */
    public function __construct(string $correlationId)
    {
        $this->correlationId = $correlationId;
    }
}