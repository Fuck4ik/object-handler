<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleContextInterface;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;
use Omasn\ObjectHandler\ObjectHandler;
use Omasn\ObjectHandler\ObjectHandlerInterface;
use Omasn\ObjectHandler\ObjectProperty;
use Omasn\ObjectHandler\ViolationFactory;
use Omasn\ObjectHandler\ViolationFactoryInterface;
use ReflectionException;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\ConstraintViolationList;

final class HandleArrayIterationType extends HandleType
{
    public const ID = 'collection';

    private ObjectHandlerInterface $handler;
    private ViolationFactoryInterface $violationFactory;

    public function __construct(ObjectHandlerInterface $handler, ViolationFactoryInterface $violationFactory = null)
    {
        $this->handler = $handler;
        $this->violationFactory = $violationFactory ?? new ViolationFactory();
    }

    public function getId(): string
    {
        return self::ID;
    }

    /**
     * @throws HandlerException
     * @throws ReflectionException
     * @throws ViolationListException
     * @throws ObjectHandlerException
     */
    public function resolveValue(HandleProperty $handleProperty, HandleContextInterface $context): array
    {
        $values = $handleProperty->getInitialValue();
        if (!is_iterable($values)) {
            throw new InvalidHandleValueException($handleProperty, 'Value must be iterable');
        }
        $violationList = new ConstraintViolationList();

        $collectionResult = [];
        $valueType = $this->getCollectionType($handleProperty);
        foreach ($values as $key => $value) {
            $handlePropertyValue = new HandleProperty(
                new ObjectProperty($key . '.' . $handleProperty->getPropertyPath(), $valueType, false),
                $value
            );

            if (null === $value) {
                throw new InvalidHandleValueException($handlePropertyValue, 'Iterable value must be not null');
            }

            if ($valueType->isCollection()) {
                $collectionResult[$key] = $this->resolveValue($handlePropertyValue, $context);
            } elseif (null !== $className = $valueType->getClassName()) {
                try {
                    $collectionResult[$key] = $this->handler->handle($className, $value, $context);
                } catch (ViolationListException $e) {
                    foreach ($e->getViolationList() as $violation) {
                        $violationList->add($this->violationFactory->fromViolationParent(
                            $violation,
                            $handlePropertyValue->getPropertyPath()
                        ));
                    }
                }
            } else {
                $this->handler->resolveHandleProperty($handlePropertyValue, $violationList, $context);
                if ($handlePropertyValue->isHandled()) {
                    $collectionResult[$key] = $handlePropertyValue->getValue();
                }
            }
        }

        if ($violationList->count() > 0) {
            throw new ViolationListException($violationList);
        }

        return $collectionResult;
    }

    public function supports(HandleProperty $handleProperty): bool
    {
        return $handleProperty->getType()->getBuiltinType() === Type::BUILTIN_TYPE_ARRAY
            && $handleProperty->getType()->isCollection();
    }

    /**
     * @throws HandlerException
     */
    private function getCollectionType(HandleProperty $handleProperty): Type
    {
        $types = $handleProperty->getType()->getCollectionValueTypes();

        if (0 === count($types)) {
            return ObjectHandler::getUndefinedType();
        }

        if (count($types) > 1) {
            throw new HandlerException(sprintf('Union types are not allowed (%s)', $handleProperty->getPropertyPath()));
        }

        return $types[0];
    }
}
