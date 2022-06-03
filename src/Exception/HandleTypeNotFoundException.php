<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Exception;

final class HandleTypeNotFoundException extends HandlerException
{
    public function __construct(string $type)
    {
        parent::__construct(sprintf('HandleType not found for type "%s"', $type));
    }
}
