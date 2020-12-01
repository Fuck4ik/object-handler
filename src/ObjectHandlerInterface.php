<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;

interface ObjectHandlerInterface
{
    public function addHandleType(HandleTypeInterface $handleType): void;

    /**
     * @throws \ReflectionException
     * @throws HandlerException
     */
    public function handle(object $object, array $data, array $context = []): ViolationPropertyMapInterface;

    /**
     * @throws HandlerException
     * @throws ObjectHandlerException
     */
    public function handleProperty(HandleProperty $handleProperty, array $context = []): HandleProperty;
}