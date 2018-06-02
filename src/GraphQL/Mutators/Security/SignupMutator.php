<?php

declare(strict_types=1);

namespace App\GraphQL\Mutators\Security;

use App\Builders\UserBuilder;
use App\Exceptions\GraphQLException;
use App\Form\Request\SignupRequest;
use App\Form\SignupType;
use App\Repository\UserRepository;
use App\Security\SecurityUser;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignupMutator implements MutationInterface
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
     * @var UserBuilder
     */
    private $userBuilder;

    /**
     * @param FormFactoryInterface         $formFactory
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository               $userRepository
     * @param UserBuilder                  $userBuilder
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        UserBuilder $userBuilder
    ) {
        $this->formFactory = $formFactory;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->userBuilder = $userBuilder;
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

        /** @var SignupRequest $request */
        $request = new SignupRequest();
        $form = $this->formFactory
            ->create(SignupType::class, $request)
        ;

        $form->submit($input);
        if (!($form->isSubmitted() && $form->isValid())) {
            throw GraphQLException::fromFormErrors($form);
        }

        /** @var \App\Entity\User $user */
        $user = $this->userBuilder
            ->create()
            ->withEmail($request->getEmail())
            ->withValidated(false)
            ->withActive(true)
            ->withValidationToken(Uuid::uuid4()->toString())
            ->build()
        ;

        $securityUser = new SecurityUser($user, $user->getRoles());
        $encodedPassword = $this->passwordEncoder
            ->encodePassword($securityUser, $request->getPassword())
        ;
        $user->setPassword($encodedPassword);

        $this->userRepository->save($user);

        return ['result' => true];
    }
}
