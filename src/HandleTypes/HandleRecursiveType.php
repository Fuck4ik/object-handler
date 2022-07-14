<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\Exception\UnionTypeException;
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
     * @throws ReflectionException
     * @throws HandlerException
     */
    public function resolveValue(?string $class, HandleProperty $handleProperty, HandleContextInterface $context): object
    {
        $data = $handleProperty->getInitialValue();
        if (!is_array($data)) {
            throw new InvalidHandleValueException(
                $handleProperty,
                sprintf('Expected of type "array", "%s" given', get_debug_type($data))
            );
        }

        if (null === $className = $this->extractClassName($handleProperty)) {
            throw new \RuntimeException('HandleType must not be resolved without first handling it through support');
        }

        return $this->handler->handle($className, $data, $context);
    }

    /**
     * @throws UnionTypeException
     */
    private function extractClassName(HandleProperty $handleProperty): ?string
    {
        $type = $handleProperty->getType();
        if ($type->isCollection()) {
            $collectionTypes = $type->getCollectionValueTypes();
            if (1 !== count($collectionTypes)) {
                throw new UnionTypeException($handleProperty->getPropertyPath());
            }

            return $collectionTypes[0]->getClassName();
        }

        return $type->getClassName();
    }

    /**
     * @throws UnionTypeException
     */
    public function supports(HandleProperty $handleProperty): bool
    {
        if (null === $callback = $this->support) {
            return false;
        }

        if (null === $className = $this->extractClassName($handleProperty)) {
            return false;
        }

        return $callback($handleProperty, $className);
    }
}
