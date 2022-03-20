<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Symfony\Component\PropertyInfo\Type;

final class ObjectProperty
{
    private string $name;
    private Type $type;
    private bool $isDefaultValue;

    public function __construct(string $name, Type $type, bool $isDefaultValue)
    {
        $this->name = $name;
        $this->type = $type;
        $this->isDefaultValue = $isDefaultValue;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function isDefaultValue(): bool
    {
        return $this->isDefaultValue;
    }
}
