<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Extractor;

final class PropertyDefaultValueExtractor implements DefaultValueExtractorInterface
{
    public function hasDefaultValue(string $class, string $property, array $context = []): bool
    {
        if (PHP_VERSION_ID < 80000) {
            return false;
        }

        return (new \ReflectionProperty($class, $property))->hasDefaultValue();
    }
}
