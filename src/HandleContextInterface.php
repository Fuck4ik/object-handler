<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

interface HandleContextInterface
{
    public function isIgnoreMissingData(): bool;

    public function getPreHandleValueCallback(): ?callable;
}
