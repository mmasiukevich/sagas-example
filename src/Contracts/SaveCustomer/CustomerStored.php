<?php

declare(strict_types = 1);

namespace App\Contracts\SaveCustomer;

/**
 * Customer successful stored in database
 *
 * @internal
 *
 * @see SaveCustomer
 *
 * @property-read string $correlationId
 * @property-read string $customerId
 */
final class CustomerStored
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