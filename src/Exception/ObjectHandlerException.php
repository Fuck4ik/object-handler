<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Exception;

use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\ViolationPropertyMapInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class ObjectHandlerException extends \Exception
{
    protected function getStatusCode(): int
    {
        return 400;
    }

    private HandleProperty $property;

    public function __construct(
        HandleProperty $property,
        string $message,
        int $code = null,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code ?: $this->getStatusCode(), $previous);
        $this->property = $property;
    }

    public function getProperty(): HandleProperty
    {
        return $this->property;
    }

    /**
     * @return ConstraintViolationList|ViolationPropertyMapInterface
     */
    public function getViolationList()
    {
        if ($violationsMap = $this->getViolationPropertyMap()) {
            return $violationsMap;
        }

        return new ConstraintViolationList([
            $this->property->buildViolation($this->getMessage())
        ]);
    }

    protected function getViolationPropertyMap(): ?ViolationPropertyMapInterface
    {
        return null;
    }

    protected function buildViolation(): ConstraintViolationInterface
    {
        return $this->property->buildViolation($this->getMessage());
    }
}