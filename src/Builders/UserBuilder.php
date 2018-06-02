<?php

declare (strict_types = 1);

namespace App\Builders;

use App\Entity\User;

class UserBuilder
{
    /**
     * @var UserInterface
     */
    private $user;

    public function __construct()
    {
    }

    public function create(): self
    {
        $this->user = new User();

        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function withFirstname(string $firstname): self
    {
        $this->user->setFirstname($firstname);

        return $this;
    }

    public function withLastname(string $lastname): self
    {
        $this->user->setLastname($lastname);

        return $this;
    }

    public function withUsername(string $username): self
    {
        $this->user->setUsername($username);

        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->user->setEmail($email);

        return $this;
    }

    public function withPassword(string $password): self
    {
        $this->user->setPassword($password);

        return $this;
    }

    public function withValidationDate(\DateTime $validationDate): self
    {
        $this->user->setValidationDate($validationDate);

        return $this;
    }

    public function withValidated(bool $validated): self
    {
        $this->user->setValidated($validated);

        return $this;
    }

    public function withActive(bool $active): self
    {
        $this->user->setActive($active);

        return $this;
    }

    public function withApiToken(string $apiToken): self
    {
        $this->user->setApiToken($apiToken);

        return $this;
    }

    public function withValidationToken(string $validationToken): self
    {
        $this->user->setValidationToken($validationToken);

        return $this;
    }

    public function withResetToken(string $resetToken): self
    {
        $this->user->setResetToken($resetToken);

        return $this;
    }

    public function build(): User
    {
        return $this->user;
    }
}
