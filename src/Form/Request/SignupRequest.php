<?php

declare (strict_types = 1);

namespace App\Form\Request;

use App\Validator\Constraints as AssertApp;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @AssertApp\UniqueEntity("email", entityClass="App\Entity\User", message="validation_email_not_unique")
 */
class SignupRequest
{
    /**
     * @Assert\NotBlank(message="validation_email_empty")
     * @Assert\Email(message="validation_email_invalid")
     *
     * @var string
     */
    public $email;

    /**
     * @Assert\NotBlank(message="validation_password_empty")
     *
     * @var string
     */
    public $password;

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }
}
