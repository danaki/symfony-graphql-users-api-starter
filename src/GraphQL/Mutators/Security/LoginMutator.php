<?php

declare(strict_types=1);

namespace App\GraphQL\Mutators\Security;

use App\Exceptions\GraphQLException;
use App\Form\LoginType;
use App\Form\Request\LoginRequest;
use App\Repository\UserRepository;
use App\Security\SecurityUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginMutator implements MutationInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtTokenManagerInterface;

    /**
     * @param FormFactoryInterface         $formFactory
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository               $userRepository
     * @param JWTTokenManagerInterface     $jwtTokenManagerInterface
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        JWTTokenManagerInterface $jwtTokenManagerInterface
    ) {
        $this->formFactory = $formFactory;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->jwtTokenManagerInterface = $jwtTokenManagerInterface;
    }

    /**
     * @param Argument $args
     */
    public function __invoke(Argument $args)
    {
        $input = $args['input'] ?? [];
        if (!$input) {
            throw GraphQLException::fromString('bad_request');
        }

        /** @var LoginRequest $request */
        $request = new LoginRequest();
        $form = $this->formFactory
            ->create(LoginType::class, $request)
        ;

        $form->submit($input);
        if (!($form->isSubmitted() && $form->isValid())) {
            throw GraphQLException::fromFormErrors($form);
        }

        /** @var App\Entity\User $user */
        $user = $this->userRepository
            ->findUserByEmail($request->getEmail())
        ;

        if (!$user) {
            throw GraphQLException::fromString('login_failed');
        }

        if (!$user->getActive()) {
            throw GraphQLException::fromString('user_disabled');
        }

        if (!$user->getValidated()) {
            throw GraphQLException::fromString('user_not_validated');
        }

        $securityUser = new SecurityUser($user, $user->getRoles());

        if ($this->passwordEncoder->isPasswordValid($securityUser, $request->getPassword())) {
            $token = $this->jwtTokenManagerInterface
                ->create($securityUser)
            ;

            return ['token' => $token];
        }

        throw GraphQLException::fromString('login_failed');
    }
}
