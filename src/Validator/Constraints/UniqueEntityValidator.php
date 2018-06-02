<?php

// src/Validator/Constraints/UniqueEntityValidator.php

namespace App\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator as BaseValidator;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Unique entity validator for form request objects.
 *
 * Use at own risk! Non-persisted changes to an entity will be reset after the validation process.
 */
class UniqueEntityValidator extends BaseValidator
{
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry);
    }

    public function validate($object, Constraint $constraint)
    {
        if ($constraint->em) {
            $entityManager = $this->registry->getManager($constraint->em);
            if (!$entityManager) {
                throw new ConstraintDefinitionException(sprintf('Object manager "%s" does not exist.', $constraint->em));
            }
        } else {
            $entityManager = $this->registry->getManagerForClass($constraint->entityClass);
            if (!$entityManager) {
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', $constraint->entityClass));
            }
        }

        $accessor = new PropertyAccessor();

        /** @var ClassMetadata $class */
        $class = $entityManager->getClassMetadata($constraint->entityClass);
        $entity = $class->newInstance();
        $fields = (array) $constraint->fields;
        $identifier = null;

        if ($accessor->isReadable($object, 'id')) {
            $identifier = $accessor->getValue($object, 'id');
            if ($identifier) {
                /** @var EntityManager $entityManager */
                $entity = $entityManager->getReference($constraint->entityClass, $identifier);
            }
        }

        foreach ($fields as $fieldName) {
            if (!$class->hasField($fieldName) && !$class->hasAssociation($fieldName)) {
                throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $fieldName));
            }
            $value = $accessor->getValue($object, $fieldName);
            $class->reflFields[$fieldName]->setValue($entity, $value);
        }

        parent::validate($entity, $constraint);
        if ($identifier) {
            $entityManager->refresh($entity);
        }
    }
}
