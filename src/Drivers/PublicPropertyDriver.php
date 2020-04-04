<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Drivers;

use Omasn\ObjectHandler\HandleDriverInterface;

class PublicPropertyDriver implements HandleDriverInterface
{
    /**
     * @inheritDoc
     */
    public function getPropertyFilters(): int
    {
        return \ReflectionProperty::IS_PUBLIC;
    }

    /**
     * @inheritDoc
     */
    public function setPropertyValue($object, \ReflectionProperty $property, $value): void
    {
        $property->setValue($object, $value);
    }
}