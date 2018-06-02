<?php

declare(strict_types=1);

namespace App\Exceptions;

use Overblog\GraphQLBundle\Error\UserErrors;
use Symfony\Component\Form\FormInterface;

class GraphQLException extends UserErrors
{
    private function __construct(array $errors, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($errors, $message, $code, $previous);
    }

    /**
     * @param string $message
     */
    public static function fromString($message)
    {
        return new self([$message]);
    }

    /**
     * @param FormInterface $form
     */
    public static function fromFormErrors(FormInterface $form)
    {
        return new self(self::getPlainErrors($form));
    }

    /**
     * @param FormInterface $form
     */
    public static function getPlainErrors($form)
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors = array_merge($errors, static::getPlainErrors($child));
            }
        }

        return $errors;
    }
}
