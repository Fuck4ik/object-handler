<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;

final class ViolationFactory implements ViolationFactoryInterface
{
    public function createNotBlank(string $parameterName, string $root = ''): ConstraintViolation
    {
        return new ConstraintViolation(
            'This value should not be blank.',
            null,
            [],
            $root,
            $parameterName,
            null
        );
    }

    public function createFromException(ObjectHandlerException $exception): ConstraintViolation
    {
        $handleProperty = $exception->getProperty();

        return new ConstraintViolation(
            $exception->getMessage(),
            null,
            [],
            '',
            $handleProperty->getPropertyPath(),
            $handleProperty->getInitialValue()
        );
    }

    public function fromViolationParent(
        ConstraintViolationInterface $violation,
        string $parentPropertyPath
    ): ConstraintViolationInterface {
        $propertyPath = $parentPropertyPath;
        if ('' !== trim($violation->getPropertyPath())) {
            $propertyPath .= '.' . $violation->getPropertyPath();
        }

        return new ConstraintViolation(
            $violation->getMessage(),
            $violation->getMessageTemplate(),
            $violation->getParameters(),
            $violation->getRoot(),
            $propertyPath,
            $violation->getInvalidValue()
        );
    }
}
