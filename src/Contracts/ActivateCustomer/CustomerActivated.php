<?php

declare(strict_types = 1);

namespace App\Contracts\ActivateCustomer;

/**
 * Customer successful activated
 *
 * @internal
 *
 * @see ActivateCustomer
 *
 * @property-read string $correlationId
 */
final class CustomerActivated
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