<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\HandleProperty;

interface HandleRecursiveSupportCallback
{
    public function supports(HandleProperty $handleProperty, string $className): bool;
}