<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;

class HandleFloatType extends HandleType
{
    public function getId(): string
    {
        return 'float';
    }

    public function getHandleValue(HandleProperty $handleProperty, array $context = []): ?float
    {
        return (float)$handleProperty->getInitialValue();
    }

    public function supports(HandleProperty $handleProperty): bool
    {
        return 'float' === $handleProperty->getType();
    }
}