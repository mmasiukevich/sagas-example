<?php

declare(strict_types = 1);

namespace App\Contracts\Verification;

/**
 * @internal
 *
 * @see VerificationCodeReceived
 *
 * @property-read string $correlationId
 */
final class IncorrectVerificationCodeReceived
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
