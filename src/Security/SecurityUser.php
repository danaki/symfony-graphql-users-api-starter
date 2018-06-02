<?php

declare (strict_types = 1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityUser implements AdvancedUserInterface, EquatableInterface, \Serializable
{
    private $id;
    private $email;
    private $roles;
    private $password;
    private $salt;

    public function __construct(User $user, array $roles = [])
    {
        $this->id = $user->getId()->toString();
        $this->email = $user->getEmail();
        $this->password = $user->getPassword();
        $this->roles = $roles;
        $this->enabled = false;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function eraseCredentials(): void
    {
        $this->password = '';
        $this->salt = null;
    }

    public function isAccountNonExpired(): bool
    {
        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return true;
    }

    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return $this->active;
    }

    public function isEqualTo(UserInterface $user)
    {
        return $user instanceof self && $this->id === $user->getId();
    }

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->roles,
            $this->enabled,
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->email,
            $this->salt,
            $this->password,
            $this->roles,
            $this->enableds
        ) = unserialize($serialized);
    }
}
