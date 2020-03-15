<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

interface HandleTypeInterface
{
    public function getHandleValue(HandleProperty $handleProperty, array $context = []);

    public function supports(HandleProperty $handleProperty): bool;

    public function getId(): string ;
}