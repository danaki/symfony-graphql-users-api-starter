<?php

declare (strict_types = 1);

namespace App\Form\Request;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordRestoreRequest
{
    /**
     * @Assert\NotBlank(message="validation_token_empty")
     *
     * @var string
     */
    public $token;

    /**
     * @Assert\NotBlank(message="validation_password_empty")
     *
     * @var string
     */
    public $password;

    public function getToken()
    {
        return $this->token;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
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
