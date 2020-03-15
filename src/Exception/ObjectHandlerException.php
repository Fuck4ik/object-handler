<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Exception;

use Omasn\ObjectHandler\HandleProperty;

abstract class ObjectHandlerException extends \Exception
{
    protected function getStatusCode(): int
    {
        return 400;
    }

    private HandleProperty $property;

    public function __construct(
        HandleProperty $property,
        string $message,
        int $code = null,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code ?: $this->getStatusCode(), $previous);
        $this->property = $property;
    }

    public function getProperty(): HandleProperty
    {
        return $this->property;
    }
}