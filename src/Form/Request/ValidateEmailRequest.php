<?php

declare(strict_types=1);

namespace App\Form\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ValidateEmailRequest
{
    /**
     * @Assert\NotBlank(message="validation_token_empty")
     *
     * @var string
     */
    public $token;

    public function getToken()
    {
        return $this->token;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }
}
