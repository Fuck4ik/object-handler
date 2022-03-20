<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\HandleContextInterface;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;
use Symfony\Component\PropertyInfo\Type;

final class HandleBoolType extends HandleType
{
    public function getId(): string
    {
        return Type::BUILTIN_TYPE_BOOL;
    }

    public function resolveValue(HandleProperty $handleProperty, HandleContextInterface $context): bool
    {
        $value = $handleProperty->getInitialValue();

        if (!is_scalar($value)) {
            throw new InvalidHandleValueException($handleProperty,
                sprintf('Expected of type "scalar", "%s" given', get_debug_type($value))
            );
        }

        return (bool)$value;
    }
}
