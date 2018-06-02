<?php

declare (strict_types = 1);

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityUserFactory implements UserLoaderInterface
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->repository->findUserByEmail($username);

        if ($user == null) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }
}
