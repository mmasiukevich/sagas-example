<?php

declare(strict_types = 1);

namespace App\Contracts\Register;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Register customer
 *
 * @api
 *
 * @see CustomerRegistered
 * @see RegisterCustomerValidationFailed
 * @see CustomerRegistrationFailed
 *
 * @property-read string $email
 * @property-read string $login
 * @property-read string $clearPassword
 */
final class RegisterCustomer
{
    /**
     * @Assert\Uuid()
     *
     * @var string
     */
    public $requestId;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @var string
     */
    public $email;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $login;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $clearPassword;

    /**
     * RegisterCustomer constructor.
     *
     * @param string $email
     * @param string $login
     * @param string $clearPassword
     * @param string $requestId
     */
    public function __construct(string $email, string $login, string $clearPassword, string $requestId)
    {
        $this->email         = $email;
        $this->login         = $login;
        $this->clearPassword = $clearPassword;
        $this->requestId     = $requestId;
    }
}