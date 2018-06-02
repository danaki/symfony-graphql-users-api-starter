<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as BaseConstraint;

/**
 * @Annotation
 */
class UniqueEntity extends BaseConstraint
{
    public function getRequiredOptions()
    {
        return ['fields', 'entityClass'];
    }

    public function validatedBy()
    {
        return UniqueEntityValidator::class;
    }
}
