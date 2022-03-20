<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleContextInterface;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;
use Omasn\ObjectHandler\ObjectHandlerInterface;
use ReflectionException;

final class HandleRecursiveType extends HandleType
{
    public const ID = 'recursive';

    private ObjectHandlerInterface $handler;

    /**
     * @var null|callable
     */
    private $support;

    public function __construct(ObjectHandlerInterface $handler, callable $support = null)
    {
        $this->handler = $handler;
        $this->support = $support;
    }

    public function getId(): string
    {
        return self::ID;
    }

    /**
     * @throws HandlerException
     * @throws ReflectionException
     * @throws ViolationListException
     * @throws InvalidHandleValueException
     */
    public function resolveValue(HandleProperty $handleProperty, HandleContextInterface $context): object
    {
        $data = $handleProperty->getInitialValue();
        if (!is_array($data)) {
            throw new InvalidHandleValueException(
                $handleProperty,
                sprintf('Expected of type "array", "%s" given', get_debug_type($data))
            );
        }

        return $this->handler->handle($handleProperty->getType()->getClassName(), $data, $context);
    }

    public function supports(HandleProperty $handleProperty): bool
    {
        if (null === $callback = $this->support) {
            return false;
        }

        return $callback($handleProperty);
    }
}
