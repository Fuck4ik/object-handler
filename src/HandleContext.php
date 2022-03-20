<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

final class HandleContext implements HandleContextInterface
{
    private bool $isIgnoreMissingData;

    /**
     * @var null|callable
     */
    private $preHandleValue;

    public function __construct(bool $isIgnoreMissingData = true, ?callable $preHandleValue = null)
    {
        $this->isIgnoreMissingData = $isIgnoreMissingData;
        $this->preHandleValue = $preHandleValue;
    }

    public function isIgnoreMissingData(): bool
    {
        return $this->isIgnoreMissingData;
    }

    public function getPreHandleValueCallback(): ?callable
    {
        return $this->preHandleValue;
    }
}
