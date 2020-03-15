<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;

class HandleBoolType extends HandleType
{
    public function getId(): string
    {
        return 'bool';
    }

    public function getHandleValue(HandleProperty $handleProperty, array $context = []): ?bool
    {
        return (bool)$handleProperty->getInitialValue();
    }

    public function supports(HandleProperty $handleProperty): bool
    {
        return 'bool' === $handleProperty->getType();
    }
}