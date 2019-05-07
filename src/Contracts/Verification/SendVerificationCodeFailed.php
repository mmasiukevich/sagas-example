<?php

declare(strict_types = 1);

namespace App\Contracts\Verification;

/**
 * @internal
 *
 * @see SendVerificationCode
 *
 * @property-read string $correlationId
 * @property-read string $reason
 */
final class SendVerificationCodeFailed
{
    /**
     * @var string
     */
    public $correlationId;

    /**
     * @var string
     */
    public $reason;

    /**
     * @param string $correlationId
     * @param string $reason
     */
    public function __construct(string $correlationId, string $reason)
    {
        $this->correlationId = $correlationId;
        $this->reason        = $reason;
    }
}