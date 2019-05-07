<?php

declare(strict_types = 1);

namespace App\Contracts\ActivateCustomer;

/**
 * Activate customer
 *
 * @internal
 *
 * @see CustomerActivated
 * @see ActivateCustomerFailed
 *
 * @property-read string $correlationId
 * @property-read string $customerId
 */
final class ActivateCustomer
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
     * @param string $correlationId
     * @param string $customerId
     */
    public function __construct(string $correlationId, string $customerId)
    {
        $this->correlationId = $correlationId;
        $this->customerId    = $customerId;
    }
}