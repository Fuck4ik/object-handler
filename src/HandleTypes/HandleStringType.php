<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\HandleContextInterface;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;
use Symfony\Component\PropertyInfo\Type;

final class HandleStringType extends HandleType
{
    public function getId(): string
    {
        return Type::BUILTIN_TYPE_STRING;
    }

    public function resolveValue(HandleProperty $handleProperty, HandleContextInterface $context): ?string
    {
        $value = $handleProperty->getInitialValue();

        if (is_array($value)) {
            throw new InvalidHandleValueException($handleProperty,
                'Expected of type "string", "array" given'
            );
        }

        return (string)$value;
    }
}
