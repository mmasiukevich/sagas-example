<?php

declare(strict_types = 1);

namespace App\Contracts\Register;

/**
 * @api
 *
 * @see RegisterCustomer
 *
 * @property-read string $requestId
 * @property-read string $reason
 */
final class CustomerRegistrationFailed
{
    /**
     * @var string
     */
    public $requestId;

    /**
     * @var string
     */
    public $reason;

    /**
     * @param string $requestId
     * @param string $reason
     */
    public function __construct(string $requestId, string $reason)
    {
        $this->requestId = $requestId;
        $this->reason    = $reason;
    }
}