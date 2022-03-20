<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Symfony\Component\Validator\ConstraintViolationInterface;

interface ViolationFactoryInterface
{
    public function createNotBlank(string $parameterName, string $root = ''): ConstraintViolationInterface;

    public function createFromException(ObjectHandlerException $exception): ConstraintViolationInterface;

    public function fromViolationParent(
        ConstraintViolationInterface $violation,
        string $parentPropertyPath
    ): ConstraintViolationInterface;
}
