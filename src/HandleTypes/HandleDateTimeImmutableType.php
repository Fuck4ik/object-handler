<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\HandleTypes;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Omasn\ObjectHandler\Exception\InvalidHandleValueException;
use Omasn\ObjectHandler\HandleContextInterface;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleType;

final class HandleDateTimeImmutableType extends HandleType
{
    private ?string $format;
    private ?DateTimeZone $timeZone;

    public function __construct(?string $format = null, ?DateTimeZone $timeZone = null)
    {
        $this->format = $format;
        $this->timeZone = $timeZone;
    }

    public function getId(): string
    {
        return DateTimeImmutable::class;
    }

    public function resolveValue(HandleProperty $handleProperty, HandleContextInterface $context): DateTimeImmutable
    {
        $value = $handleProperty->getInitialValue();

        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        if (!is_scalar($value)) {
            throw new InvalidHandleValueException(
                $handleProperty,
                sprintf('Expected of type "scalar", "%s" given', get_class($value))
            );
        }

        if (null !== $this->format) {
            return $this->createFromFormat($handleProperty);
        }

        return $this->createFromDefaultFormat($handleProperty);
    }

    public function supports(HandleProperty $handleProperty): bool
    {
        return DateTimeImmutable::class === $handleProperty->getType()->getClassName();
    }

    /**
     * @throws InvalidHandleValueException
     */
    private function createFromFormat(HandleProperty $handleProperty): DateTimeImmutable
    {
        $value = $handleProperty->getInitialValue();
        $dateTime = DateTimeImmutable::createFromFormat($this->format, $value, $this->timeZone);

        if (false === $dateTime) {
            $errorMessage = DateTimeImmutable::getLastErrors() ?: ['Invalid format'];

            throw new InvalidHandleValueException($handleProperty, implode(' ', $errorMessage));
        }

        return $dateTime;
    }

    /**
     * @throws InvalidHandleValueException
     */
    private function createFromDefaultFormat(HandleProperty $handleProperty): DateTimeImmutable
    {
        $value = $handleProperty->getInitialValue();

        if (is_numeric($value)) {
            $value = '@' . $value;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Exception $e) {
            throw new InvalidHandleValueException($handleProperty, $e->getMessage(), 0, $e);
        }
    }
}
