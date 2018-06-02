<?php

declare(strict_types=1);

namespace App\Form\Request;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetRequest
{
    /**
     * @Assert\NotBlank(message="validation_email_empty")
     * @Assert\Email(message="validation_email_invalid")
     *
     * @var string
     */
    public $email;

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }
}
