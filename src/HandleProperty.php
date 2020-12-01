<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Symfony\Component\Validator\ConstraintViolation;

final class HandleProperty
{
    /** @var mixed */
    private $initialValue;
    /** @var mixed */
    private $value;
    private string $propertyPath;
    private string $type;
    private bool $allowsNull;
    private bool $isInitialized;

    /**
     * @param mixed $initialValue
     */
    public function __construct(
        $initialValue,
        string $propertyPath,
        string $type,
        bool $allowsNull,
        bool $isInitialized
    ) {
        $this->initialValue = $initialValue;
        $this->value = $initialValue;
        $this->propertyPath = $propertyPath;
        $this->type = $type;
        $this->allowsNull = $allowsNull;
        $this->isInitialized = $isInitialized;
    }

    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    public function getInitialValue()
    {
        return $this->initialValue;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function buildViolation(string $message, array $parameters = []): ConstraintViolation
    {
        return new ConstraintViolation(
            $message,
            null,
            $parameters,
            $this->initialValue,
            $this->propertyPath,
            $this->value,
        );
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function isNull(): bool
    {
        return null === $this->getInitialValue() && $this->allowsNull();
    }

    public function isInitialized(): bool
    {
        return $this->isInitialized;
    }
}