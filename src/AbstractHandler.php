<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandlerException;
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
     * @throws HandlerException
     * @throws ObjectHandlerException
     * @throws ViolationListException
     */
    public function handleProperty(HandleProperty $handleProperty, HandleContextInterface $context): HandleProperty
    {
        if (null === $handleProperty->getInitialValue()) {
            throw new InvalidHandleValueException($handleProperty, 'InitialValue must be not null');
        }

        $handleType = $this->getHandleType($handleProperty);

        try {
            if (null === $resolveValue = $handleType->resolveValue($handleProperty, $context)) {
                throw new InvalidHandleValueException($handleProperty, 'resolveValue must be not null');
            }
        } catch (ObjectHandlerException|ViolationListException|HandlerException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new InvalidHandleValueException($handleProperty, $e->getMessage(), 0, $e);
        }
        $handleProperty->setValue($resolveValue);

        return $handleProperty;
    }

    /**
     * @throws HandlerException
     */
    public function resolveHandleProperty(
        HandleProperty $handleProperty,
        ConstraintViolationListInterface $violationList,
        HandleContextInterface $context
    ): void {
        $preHandleValueCallback = $context->getPreHandleValueCallback();
        if (null !== $preHandleValueCallback && !$preHandleValueCallback($handleProperty)) {
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
            $this->handleProperty($handleProperty, $context);
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
     * @throws HandlerException
     */
    abstract protected function getHandleType(HandleProperty $handleProperty): HandleTypeInterface;
}
