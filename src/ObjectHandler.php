<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\Exception\NotBlankHandleValueException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectHandler implements ObjectHandlerInterface
{
    /**
     * @var HandleTypeInterface[]
     */
    protected array $handleTypes = [];

    private HandleDriverInterface $driver;

    public function __construct(HandleDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function addHandleType(HandleTypeInterface $handleType): void
    {
        $this->handleTypes[$handleType->getId()] = $handleType;
    }

    /**
     * @throws \ReflectionException
     * @throws HandlerException
     */
    public function handle(object $object, array $data, array $context = []): ViolationPropertyMapInterface
    {
        $reflector = new \ReflectionClass($object);
        $reflProperties = $reflector->getProperties($this->driver->getPropertyFilters());
        $violationsMap = new ViolationPropertyMap();
        $validator = $this->getValidator($context);

        foreach ($reflProperties as $reflProperty) {
            $propertyName = $reflProperty->getName();

            if (array_key_exists($propertyName, $data)) {
                $handleValue = $data[$propertyName];
            } else {
                if ($reflProperty->isInitialized($object) && null !== $reflProperty->getValue($object)) {
                    continue;
                }
                $handleValue = null;
            }

            if (null !== $validator) {
                $propertyViolationList = $validator->validatePropertyValue($object, $propertyName, $handleValue);
                if ($propertyViolationList->count() > 0) {
                    $violationsMap->set($propertyName, $propertyViolationList);
                    continue;
                }
            }

            try {
                $handledProperty = $this->createHandleProperty($reflProperty, $handleValue, $object);
                $this->handleProperty($handledProperty, $context);
            } catch (ObjectHandlerException $e) {
                $violationsMap->set($propertyName, $e->getViolationList());
                continue;
            }

            $this->driver->setPropertyValue($object, $reflProperty, $handledProperty->getValue());
        }

        return $violationsMap;
    }

    /**
     * @param mixed $value
     *
     * @throws HandlerException
     */
    private function createHandleProperty(\ReflectionProperty $reflProperty, $value, object $object): HandleProperty
    {
        $propertyType = $reflProperty->getType();
        if (!$propertyType instanceof \ReflectionNamedType) {
            throw new HandlerException(sprintf('Property "%s" not have named type', $reflProperty->getName()));
        }

        try {
            $isInitialized = $reflProperty->isInitialized($object);
        } catch (\ReflectionException $e) {
            $reflProperty->setAccessible(true);
            $isInitialized = $reflProperty->isInitialized($object);
            $reflProperty->setAccessible(false);
        }

        return new HandleProperty(
            $value,
            $reflProperty->getName(),
            $propertyType->getName(),
            $propertyType->allowsNull(),
            $isInitialized
        );
    }

    /**
     * @throws HandlerException
     * @throws ObjectHandlerException
     */
    public function handleProperty(HandleProperty $handleProperty, array $context = []): HandleProperty
    {
        $handleType = $this->getHandleType($handleProperty);

        if (null === $handleType) {
            throw new HandlerException(sprintf('HandleType not found for type "%s"', $handleProperty->getType()));
        }

        if (!$handleProperty->isInitialized() && null === $handleProperty->getInitialValue() && !$handleProperty->allowsNull()) {
            throw new NotBlankHandleValueException($handleProperty, 'This value should not be blank.');
        }

        if ($handleProperty->isNull()) {
            return $handleProperty;
        }

        try {
            $resultValue = $handleType->getHandleValue($handleProperty, $context);
        } catch (ObjectHandlerException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new InvalidHandleValueException($handleProperty, $e->getMessage(), null, $e);
        }
        $handleProperty->setValue($resultValue);

        return $handleProperty;
    }

    protected function getValidator(array $context): ?ValidatorInterface
    {
        $validator = $context['validator'] ?? null;

        if ($validator instanceof ValidatorInterface) {
            return $validator;
        }

        return null;
    }

    protected function getHandleType(HandleProperty $propertyValue): ?HandleTypeInterface
    {
        foreach ($this->handleTypes as $handleType) {
            if ($handleType->supports($propertyValue)) {
                return $handleType;
            }
        }

        return null;
    }
}
