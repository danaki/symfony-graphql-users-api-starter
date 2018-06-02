<?php

declare(strict_types=1);

namespace App\GraphQL\Mutators\Security;

use App\Exceptions\GraphQLException;
use App\Form\PasswordRestoreType;
use App\Form\Request\PasswordRestoreRequest;
use App\Repository\UserRepository;
use App\Security\SecurityUser;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordRestoreMutator implements MutationInterface
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
     * @param FormFactoryInterface         $formFactory
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository               $userRepository
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository
    ) {
        $this->formFactory = $formFactory;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
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

        /** @var PasswordRestoreRequest $request */
        $request = new PasswordRestoreRequest();
        $form = $this->formFactory
            ->create(PasswordRestoreType::class, $request)
        ;

        $form->submit($input);
        if (!($form->isSubmitted() && $form->isValid())) {
            throw GraphQLException::fromFormErrors($form);
        }

        /** @var \App\Entity\User $user */
        $user = $this->userRepository
            ->findOneBy([
                'resetToken' => $request->getToken(),
            ]);

        if (!$user) {
            throw GraphQLException::fromString('user_not_found');
        }

        $securityUser = new SecurityUser($user, $user->getRoles());
        $password = $this->passwordEncoder
            ->encodePassword($securityUser, $request->getPassword())
        ;

        $user->setPassword($password);
        $user->setResetToken('');

        $this->userRepository
            ->save($user)
        ;

        return ['result' => true];
    }
}
