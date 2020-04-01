<?php declare(strict_types=1);

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

        $violationsMap = new ViolationPropertyMap();
        $validator = $this->getValidator($context);
        foreach ($reflector->getProperties() as $refProperty) {
            $propertyName = $refProperty->getName();
            $handleValue = $data[$propertyName] ?? null;

            if (null !== $validator) {
                $propertyViolationList = $validator->validatePropertyValue($object, $propertyName, $handleValue);
                if ($propertyViolationList->count() > 0) {
                    $violationsMap->set($propertyName, $propertyViolationList);
                    continue;
                }
            }

            try {
                $handleProperty = $this->handleProperty($refProperty, $handleValue, $context);
            } catch (ObjectHandlerException $e) {
                $violationsMap->set($propertyName, $e->getViolationList());
                continue;
            }

            $refProperty->setValue($object, $handleProperty->getValue());
        }

        return $violationsMap;
    }

    /**
     * @param \ReflectionProperty $refProperty
     * @param $value
     * @param array $context
     *
     * @return HandleProperty
     * @throws HandlerException
     * @throws ObjectHandlerException
     */
    public function handleProperty(\ReflectionProperty $refProperty, $value, array $context = []): HandleProperty
    {
        $handleProperty = new HandleProperty($value, $refProperty);
        $handleType = $this->getHandleType($handleProperty);

        if (null === $handleType) {
            throw new HandlerException(sprintf('HandleType not found for type "%s"', $handleProperty->getType()));
        }

        if (null === $value && !$handleProperty->allowsNull()) {
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

    protected function getValidator(array $context): ValidatorInterface
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
