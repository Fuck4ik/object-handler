<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Extractor;

interface DefaultValueExtractorInterface
{
    public function hasDefaultValue(string $class, string $property, array $context = []): bool;
}
