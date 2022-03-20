<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\HandleContextInterface;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;
use Symfony\Component\PropertyInfo\Type;

final class HandleIntType extends HandleType
{
    public function getId(): string
    {
        return Type::BUILTIN_TYPE_INT;
    }

    public function resolveValue(HandleProperty $handleProperty, HandleContextInterface $context): ?int
    {
        $value = $handleProperty->getInitialValue();

        if (!is_scalar($value)) {
            throw new InvalidHandleValueException(
                $handleProperty,
                sprintf('Expected of type "scalar", "%s" given', get_debug_type($value))
            );
        }

        if (!is_numeric($value)) {
            throw new InvalidHandleValueException(
                $handleProperty,
                sprintf('Expected of type "numeric", "%s" given', get_debug_type($value))
            );
        }

        return (int)$value;
    }
}
