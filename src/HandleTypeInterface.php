<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Omasn\ObjectHandler\Exception\ViolationListException;

interface HandleTypeInterface
{
    public function getId(): string;

    /**
     * @throws ObjectHandlerException
     * @throws ViolationListException
     */
    public function resolveValue(HandleProperty $handleProperty, HandleContextInterface $context);

    public function supports(HandleProperty $handleProperty): bool;
}
