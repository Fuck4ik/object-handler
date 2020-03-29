<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Symfony\Component\Validator\ConstraintViolationList;

interface ObjectHandlerInterface
{
    public function addHandleType(HandleTypeInterface $handleType): void;

    public function handle($object, array $data, array $context = []): ConstraintViolationList;

    /**
     * @param \ReflectionProperty $refProperty
     * @param $value
     * @param array $context
     *
     * @return HandleProperty
     * @throws HandlerException
     * @throws ObjectHandlerException
     */
    public function handleProperty(\ReflectionProperty $refProperty, $value, array $context = []): HandleProperty;
}