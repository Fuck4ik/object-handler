<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandlerException;
use Symfony\Component\Validator\ConstraintViolation;

class HandleProperty
{
    private string $propertyPath;

    /**
     * @var mixed
     */
    private $initialValue;

    /**
     * @var mixed
     */
    private $value;

    private string $type;

    private bool $allowsNull;

    /**
     * HandleProperty constructor.
     *
     * @param $initialValue
     * @param \ReflectionProperty $property
     * @throws HandlerException
     */
    public function __construct($initialValue, \ReflectionProperty $property)
    {
        $propertyType = $property->getType();
        if (!$propertyType instanceof \ReflectionNamedType) {
            throw new HandlerException(sprintf('Property "%s" not have named type', $property->getName()));
        }
        $this->type = $propertyType->getName();
        $this->initialValue = $initialValue;
        $this->value = $initialValue;
        $this->propertyPath = $property->getName();
        $this->allowsNull = $propertyType->allowsNull();
    }

    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    public function getInitialValue()
    {
        return $this->initialValue;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function buildViolation(string $message, array $parameters = []): ConstraintViolation
    {
        return new ConstraintViolation(
            $message,
            null,
            $parameters,
            $this->initialValue,
            $this->propertyPath,
            $this->value,
        );
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }
}