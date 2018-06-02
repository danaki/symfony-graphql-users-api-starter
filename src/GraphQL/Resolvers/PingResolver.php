<?php

declare (strict_types = 1);

namespace App\GraphQL\Resolvers;

use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class PingResolver implements ResolverInterface
{
    public function __invoke()
    {
        return 'pong';
    }
}
