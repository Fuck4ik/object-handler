<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\ObjectHandlerException;

interface HandleTypeInterface
{
    /**
     * @param HandleProperty $handleProperty
     * @param array $context
     * @return mixed
     * @throws ObjectHandlerException
     */
    public function getHandleValue(HandleProperty $handleProperty, array $context = []);

    public function supports(HandleProperty $handleProperty): bool;

    public function getId(): string ;
}