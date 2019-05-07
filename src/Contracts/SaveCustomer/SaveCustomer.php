<?php

declare(strict_types = 1);

namespace App\Contracts\SaveCustomer;

/**
 * Save customer to database
 *
 * @internal
 *
 * @see CustomerStored
 * @see SaveCustomerFailed
 *
 * @property-read string $correlationId
 * @property-read string $email
 * @property-read string $login
 * @property-read string $password
 */
final class SaveCustomer
{
    /**
     * @var string
     */
    public $correlationId;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $login;

    /**
     * @var string
     */
    public $password;

    /**
     * @param string $correlationId
     * @param string $email
     * @param string $login
     * @param string $password
     */
    public function __construct(string $correlationId, string $email, string $login, string $password)
    {
        $this->correlationId = $correlationId;
        $this->email         = $email;
        $this->login         = $login;
        $this->password      = $password;
    }
}
