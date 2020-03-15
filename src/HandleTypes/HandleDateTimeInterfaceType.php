<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\Exception\InvalidDateTimeFormatException;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;

class HandleDateTimeInterfaceType extends HandleType
{
    public function getId(): string
    {
        return \DateTimeInterface::class;
    }

    public function getHandleValue(HandleProperty $handleProperty, array $context = []): ?\DateTimeInterface
    {
        $value = $handleProperty->getInitialValue();

        try {
            if (is_numeric($value)) {
                $value = new \DateTime('@'.$value);
            } else {
                $value = new \DateTime($value);
            }
        } catch (\Exception $e) {
            throw new InvalidDateTimeFormatException($handleProperty, $e->getMessage(), null, $e);
        }

        return $value;
    }

    public function supports(HandleProperty $handleProperty): bool
    {
        return \DateTimeInterface::class === $handleProperty->getType();
    }
}