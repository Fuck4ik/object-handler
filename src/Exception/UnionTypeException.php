<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Exception;

final class UnionTypeException extends HandlerException
{
    public function __construct(string $property, string $class = null)
    {
        if (null === $class) {
            $message = sprintf('Union types are not allowed (%s)', $property);
        } else {
            $message = sprintf('Union types are not allowed (%s::%s)', $class, $property);
        }
        parent::__construct($message);
    }
}
