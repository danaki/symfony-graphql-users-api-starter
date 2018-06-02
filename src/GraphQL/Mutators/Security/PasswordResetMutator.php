<?php

declare (strict_types = 1);

namespace App\GraphQL\Mutators\Security;

use App\Exceptions\GraphQLException;
use App\Form\PasswordResetType;
use App\Form\Request\PasswordResetRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordResetMutator implements MutationInterface
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

        /** @var PasswordResetRequest $request */
        $request = new PasswordResetRequest();
        $form = $this->formFactory
            ->create(PasswordResetType::class, $request)
        ;

        $form->submit($input);
        if (!($form->isSubmitted() && $form->isValid())) {
            throw GraphQLException::fromFormErrors($form);
        }

        /** @var \App\Entity\User $user */
        $user = $this->userRepository
            ->findUserByEmail($request->getEmail())
        ;

        if (!$user) {
            throw GraphQLException::fromString('user_not_found');
        }

        $user->setResetToken(Uuid::uuid4()->toString());

        $this->userRepository
            ->save($user)
        ;

        return ['result' => true];
    }
}
