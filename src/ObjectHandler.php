<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Symfony\Component\Validator\ConstraintViolationList;

class ObjectHandler implements ObjectHandlerInterface
{
    /**
     * @var HandleTypeInterface[]
     */
    protected array $handleTypes = [];

    public function addHandleType(HandleTypeInterface $handleType): void
    {
        $this->handleTypes[] = $handleType;
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
        $violationList = new ConstraintViolationList();
        $reflector = new \ReflectionClass($object);

        foreach ($reflector->getProperties() as $property) {
            $handleProperty = new HandleProperty($data[$property->getName()] ?? null, $property);
            $handleType = $this->getHandleType($handleProperty);

            if (null !== $handleType) {
                if ($this->isNull($handleProperty)) {
                    $handleProperty->setValue(null);
                } else {
                    try {
                        $handleProperty->setValue($handleType->getHandleValue($handleProperty, $context));
                    } catch (ObjectHandlerException $e) {
                        $violationList->add($handleProperty->buildViolation($e->getMessage()));
                        continue;
                    } catch (\Throwable $e) {
                        $violationList->add($handleProperty->buildViolation($e->getMessage()));
                        continue;
                    }
                }
            } else {
                throw new HandlerException(sprintf('HandleType not found for type "%s"', $handleProperty->getType()));
            }

            $property->setValue($object, $handleProperty->getValue());
        }

        return $violationList;
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

    protected function isNull(HandleProperty $handleProperty): bool
    {
        return null === $handleProperty->getInitialValue() && $handleProperty->allowsNull();
    }
}
