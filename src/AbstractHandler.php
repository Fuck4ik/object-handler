<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandleTypeNotFoundException;
use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

abstract class AbstractHandler implements ObjectHandlerInterface
{
    private ViolationFactoryInterface $violationFactory;

    public function __construct(ViolationFactoryInterface $violationFactory = null)
    {
        $this->violationFactory = $violationFactory ?? new ViolationFactory();
    }

    /**
     * @param class-string|null $class
     *
     * @throws HandleTypeNotFoundException
     * @throws ObjectHandlerException
     * @throws ViolationListException
     */
    public function handleProperty(
        ?string $class,
        HandleProperty $handleProperty,
        HandleContextInterface $context
    ): HandleProperty {
        if (null === $handleProperty->getInitialValue()) {
            throw new InvalidHandleValueException($handleProperty, 'InitialValue must be not null');
        }

        $handleType = $this->getHandleType($handleProperty);

        try {
            if (null === $resolveValue = $handleType->resolveValue($class, $handleProperty, $context)) {
                throw new InvalidHandleValueException($handleProperty, 'resolveValue must be not null');
            }
        } catch (ObjectHandlerException|ViolationListException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new InvalidHandleValueException($handleProperty, $e->getMessage(), 0, $e);
        }
        $handleProperty->setValue($resolveValue);

        return $handleProperty;
    }

    /**
     * @throws HandleTypeNotFoundException
     */
    public function resolveHandleProperty(
        ?string $class,
        HandleProperty $handleProperty,
        ConstraintViolationListInterface $violationList,
        HandleContextInterface $context
    ): void {
        $preHandleValueCallback = $context->getPreHandleValueCallback();
        if (null !== $preHandleValueCallback && !$preHandleValueCallback($handleProperty, $class)) {
            return;
        }

        if (null === $handleProperty->getInitialValue()) {
            if ($handleProperty->getType()->isNullable()) {
                $handleProperty->setValue(null);
            } else {
                $violationList->add($this->violationFactory->createNotBlank($handleProperty->getPropertyPath()));
            }

            return;
        }

        if ($handleProperty->getType()->getBuiltinType() === self::getUndefinedType()->getBuiltinType()) {
            $handleProperty->setValue($handleProperty->getInitialValue());

            return;
        }

        try {
            $this->handleProperty($class, $handleProperty, $context);
        } catch (ObjectHandlerException $e) {
            $violationList->add($this->violationFactory->createFromException($e));
        } catch (ViolationListException $e) {
            foreach ($e->getViolationList() as $violation) {
                $violationList->add($this->violationFactory->fromViolationParent(
                    $violation,
                    $handleProperty->getPropertyPath()
                ));
            }
        }
    }

    /**
     * The type is not handle
     */
    public static function getUndefinedType(): Type
    {
        $name = 'mixed';
        if (!in_array($name, Type::$builtinTypes)) {
            Type::$builtinTypes[] = $name;
        }

        return new Type($name, true);
    }

    /**
     * @throws HandleTypeNotFoundException
     */
    abstract protected function getHandleType(HandleProperty $handleProperty): HandleTypeInterface;
}
