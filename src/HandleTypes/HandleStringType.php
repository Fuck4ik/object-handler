<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;

class HandleStringType extends HandleType
{
    public function getId(): string
    {
        return 'string';
    }

    public function getHandleValue(HandleProperty $handleProperty, array $context = []): ?string
    {
        return (string)$handleProperty->getInitialValue();
    }

    public function supports(HandleProperty $handleProperty): bool
    {
        return 'string' === $handleProperty->getType();
    }
}