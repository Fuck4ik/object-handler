<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Omasn\ObjectHandler\Exception\ViolationListException;

interface HandleTypeInterface
{
    public function getId(): string;

    /**
     * @param class-string|null $class
     *
     * @throws ObjectHandlerException
     * @throws ViolationListException
     */
    public function resolveValue(?string $class, HandleProperty $handleProperty, HandleContextInterface $context);

    public function supports(HandleProperty $handleProperty): bool;
}
