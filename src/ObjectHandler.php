<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\Exception\NotBlankHandleValueException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectHandler implements ObjectHandlerInterface
{
    private HandleDriverInterface $driver;

    public function __construct(HandleDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @var HandleTypeInterface[]
     */
    protected array $handleTypes = [];

    public function addHandleType(HandleTypeInterface $handleType): void
    {
        $this->handleTypes[$handleType->getId()] = $handleType;
    }

    /**
     * @param $object
     * @param array $data
     * @param array $context
     * @return ViolationPropertyMapInterface
     * @throws \ReflectionException
     * @throws HandlerException
     */
    public function handle($object, array $data, array $context = []): ViolationPropertyMapInterface
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
                $context['is_initialized'] = $reflProperty->isInitialized($object);
                $handledProperty = $this->handleProperty($reflProperty, $handleValue, $context);
            } catch (ObjectHandlerException $e) {
                $violationsMap->set($propertyName, $e->getViolationList());
                continue;
            }

            $this->driver->setPropertyValue($object, $reflProperty, $handledProperty->getValue());
        }

        return $violationsMap;
    }

    /**
     * @param \ReflectionProperty $reflProperty
     * @param $value
     * @param array $context
     *
     * @return HandleProperty
     * @throws HandlerException
     * @throws ObjectHandlerException
     */
    public function handleProperty(\ReflectionProperty $reflProperty, $value, array $context = []): HandleProperty
    {
        $handleProperty = new HandleProperty($value, $reflProperty);
        $handleType = $this->getHandleType($handleProperty);

        if (null === $handleType) {
            throw new HandlerException(sprintf('HandleType not found for type "%s"', $handleProperty->getType()));
        }

        $isInitialized = $context['is_initialized'] ?? false;
        if (!$isInitialized && null === $value && !$handleProperty->allowsNull()) {
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
