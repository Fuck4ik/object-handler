<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Exception;

use Omasn\ObjectHandler\HandleProperty;

abstract class ObjectHandlerException extends HandlerException
{
    private HandleProperty $property;

    public function __construct(
        HandleProperty $property,
        string $message,
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->property = $property;
    }

    public function getProperty(): HandleProperty
    {
        return $this->property;
    }
}
