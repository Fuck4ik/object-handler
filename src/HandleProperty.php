<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Symfony\Component\PropertyInfo\Type;

final class HandleProperty
{
    private ObjectProperty $property;
    private $initialValue;
    private $value;
    private bool $valueHandled = false;

    public function __construct(
        ObjectProperty $property,
        $initialValue
    ) {
        $this->property = $property;
        $this->initialValue = $initialValue;
    }

    public function getPropertyPath(): string
    {
        return $this->property->getName();
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
        $this->valueHandled = true;
        $this->value = $value;
    }

    public function getType(): Type
    {
        return $this->property->getType();
    }

    public function isHandled(): bool
    {
        return $this->valueHandled;
    }
}
