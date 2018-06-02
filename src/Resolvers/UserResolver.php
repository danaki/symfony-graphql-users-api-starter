<?php

namespace App\Resolvers;

use App\Repository\UserRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class UserResolver implements MutationInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Argument $args)
    {
        $apartment = $this->userRepository->find($args['id']);

        return $apartment;
    }
}
