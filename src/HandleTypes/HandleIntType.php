<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;

class HandleIntType extends HandleType
{
    public function getId(): string
    {
        return 'int';
    }

    public function getHandleValue(HandleProperty $handleProperty, array $context = []): ?int
    {
        if (!is_numeric($handleProperty->getInitialValue())) {
            throw new InvalidHandleValueException($handleProperty, 'Invalid numeric format');
        }

        return (int)$handleProperty->getInitialValue();
    }

    public function supports(HandleProperty $handleProperty): bool
    {
        return 'int' === $handleProperty->getType();
    }
}