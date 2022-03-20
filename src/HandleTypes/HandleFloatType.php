<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\HandleContextInterface;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;
use Symfony\Component\PropertyInfo\Type;

final class HandleFloatType extends HandleType
{
    public function getId(): string
    {
        return Type::BUILTIN_TYPE_FLOAT;
    }

    public function resolveValue(HandleProperty $handleProperty, HandleContextInterface $context): float
    {
        $value = $handleProperty->getInitialValue();

        if (!is_scalar($value)) {
            throw new InvalidHandleValueException(
                $handleProperty,
                sprintf('Expected of type "scalar", "%s" given', get_debug_type($value))
            );
        }

        if (!is_float($value + 0.0)) {
            throw new InvalidHandleValueException(
                $handleProperty,
                sprintf('Expected of type "floating", "%s" given', get_debug_type($value))
            );
        }

        return (float)$value;
    }
}
