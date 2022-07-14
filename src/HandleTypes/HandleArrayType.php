<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\HandleContextInterface;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;
use Symfony\Component\PropertyInfo\Type;

final class HandleArrayType extends HandleType
{
    public function getId(): string
    {
        return Type::BUILTIN_TYPE_ARRAY;
    }

    public function resolveValue(?string $class, HandleProperty $handleProperty, HandleContextInterface $context): array
    {
        return (array)$handleProperty->getInitialValue();
    }
}
