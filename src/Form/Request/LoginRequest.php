<?php

declare (strict_types = 1);

namespace App\Form\Request;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest
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
