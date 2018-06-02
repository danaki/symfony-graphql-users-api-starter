<?php

declare(strict_types=1);

namespace App\GraphQL\Mutators\Security;

use App\Exceptions\GraphQLException;
use App\Form\Request\ValidateEmailRequest;
use App\Form\ValidateEmailType;
use App\Repository\UserRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ValidateEmailMutator implements MutationInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param FormFactoryInterface $formFactory
     * @param UserRepository       $userRepository
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        UserRepository $userRepository
    ) {
        $this->formFactory = $formFactory;
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

        /** @var ValidateEmailRequest $request */
        $request = new ValidateEmailRequest();
        $form = $this->formFactory
            ->create(ValidateEmailType::class, $request)
        ;

        $form->submit($input);
        if (!($form->isSubmitted() && $form->isValid())) {
            throw GraphQLException::fromFormErrors($form);
        }

        /** @var App\Entity\User $user */
        $user = $this->userRepository
            ->findOneBy([
                'validationToken' => $request->getToken(),
            ]);

        if (!$user) {
            throw GraphQLException::fromString('user_not_found');
        }

        $user->setValidated(true);
        $user->setActive(true);

        $this->userRepository
            ->save($user)
        ;

        return ['result' => true];
    }
}
