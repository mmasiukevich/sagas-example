<?php

declare(strict_types = 1);

namespace App\Contracts\Register;

/**
 * Invalid customer registration details
 *
 * @api
 *
 * @see RegisterCustomer
 *
 * @property-read string $requestId
 * @property-read array $violations
 */
final class RegisterCustomerValidationFailed
{
    /**
     * @var string
     */
    public $requestId;

    /**
     * @var array
     */
    public $violations;

    /**
     * RegisterCustomerValidationFailed constructor.
     *
     * @param string $requestId
     * @param array  $violations
     */
    public function __construct(string $requestId, array $violations)
    {
        $this->requestId  = $requestId;
        $this->violations = $violations;
    }
}
