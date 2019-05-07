<?php

declare(strict_types = 1);

namespace App\Contracts\Register;

/**
 * Customer successful registered
 *
 * @api RegisterCustomer
 *
 * @property-read string $requestId
 * @property-read string $customerId
 */
final class CustomerRegistered
{
    /**
     * @var string
     */
    public $requestId;

    /**
     * @var string
     */
    public $customerId;

    /**
     * @param string $requestId
     * @param string $customerId
     */
    public function __construct(string $requestId, string $customerId)
    {
        $this->requestId  = $requestId;
        $this->customerId = $customerId;
    }
}
