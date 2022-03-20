<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Extractor;

final class ConstructorDefaultValueExtractor implements DefaultValueExtractorInterface
{
    public function hasDefaultValue(string $class, string $property, array $context = []): bool
    {
        if (null === $constructor = (new \ReflectionClass($class))->getConstructor()) {
            return false;
        }

        foreach ($constructor->getParameters() as $parameter) {
            if ($parameter->getName() === $property) {
                return $parameter->isDefaultValueAvailable();
            }
        }

        return false;
    }
}
