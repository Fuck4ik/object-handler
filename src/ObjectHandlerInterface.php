<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Symfony\Component\Validator\ConstraintViolationList;

interface ObjectHandlerInterface
{
    public function addHandleType(HandleTypeInterface $handleType): void;

    public function handle($object, array $data, array $context = []): ConstraintViolationList;
}