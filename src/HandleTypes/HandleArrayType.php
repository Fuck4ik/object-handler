<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;

class HandleArrayType extends HandleType
{
    public function getId(): string
    {
        return 'array';
    }

    public function getHandleValue(HandleProperty $handleProperty, array $context = []): ?array
    {
        return (array)$handleProperty->getInitialValue();
    }

    public function supports(HandleProperty $handleProperty): bool
    {
        return 'array' === $handleProperty->getType();
    }
}