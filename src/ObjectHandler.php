<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\Exception\NotBlankHandleValueException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectHandler implements ObjectHandlerInterface
{
    private ?ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator = null)
    {
        $this->validator = $validator;
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
     * @return ConstraintViolationList
     * @throws \ReflectionException
     * @throws HandlerException
     */
    public function handle($object, array $data, array $context = []): ConstraintViolationList
    {
        $reflector = new \ReflectionClass($object);

        $violationList = new ConstraintViolationList();
        foreach ($reflector->getProperties() as $refProperty) {
            $propertyName = $refProperty->getName();
            $handleValue = $data[$propertyName] ?? null;

            if (null !== $this->validator) {
                // TODO: Переделать все на $validatorContext
                $validatorContext = $this->validator->startContext();
                $propertyViolationList = $validatorContext->validatePropertyValue($object, $propertyName, $handleValue)->getViolations();
                if ($propertyViolationList->count() > 0) {
                    $violationList->addAll($propertyViolationList);
                    continue;
                }
            }

            try {
                $handleProperty = $this->handleProperty($refProperty, $handleValue, $context);
            } catch (ObjectHandlerException $e) {
                $violationList->add($e->buildViolation());
                continue;
            }

            $refProperty->setValue($object, $handleProperty->getValue());
        }

        return $violationList;
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

        // symfony validation

        if (null === $value && !$handleProperty->allowsNull()) {
            throw new NotBlankHandleValueException($handleProperty, 'This value should not be blank.');
        }

        if ($handleProperty->isNull()) {
            return $handleProperty;
        }

        try {
            $resultValue = $handleType->getHandleValue($handleProperty, $context);
        } catch (\Throwable $e) {
            throw new InvalidHandleValueException($handleProperty, $e->getMessage(), null, $e);
        }
        $handleProperty->setValue($resultValue);

        return $handleProperty;
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
