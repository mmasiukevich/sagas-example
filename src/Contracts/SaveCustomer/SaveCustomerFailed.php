<?php

declare(strict_types = 1);

namespace App\Contracts\SaveCustomer;

/**
 * Database error occurred
 *
 * @internal
 *
 * @see SaveCustomer
 *
 * @property-read string $correlationId
 * @property-read string $reason
 */
final class SaveCustomerFailed
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
