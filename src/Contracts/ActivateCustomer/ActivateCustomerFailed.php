<?php

declare(strict_types = 1);

namespace App\Contracts\ActivateCustomer;

/**
 * Some error occurred
 *
 * @internal
 *
 * @see ActivateCustomer
 *
 * @property-read string $correlationId
 * @property-read string $reason
 */
final class ActivateCustomerFailed
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
