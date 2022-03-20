<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

abstract class HandleType implements HandleTypeInterface
{
    public function supports(HandleProperty $handleProperty): bool
    {
        return $this->getId() === $handleProperty->getType()->getBuiltinType();
    }
}
