<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Exception;

use Omasn\ObjectHandler\HandleProperty;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class RequireArgumentException extends HandlerException
{
    /**
     * @var HandleProperty[]
     */
    private array $handleValues;
    private ConstraintViolationListInterface $violationList;

    /**
     * @param HandleProperty[] $handleValues
     */
    public function __construct(array $handleValues, ConstraintViolationListInterface $violationList)
    {
        parent::__construct('You cannot initialize an object through a constructor without these arguments.');
        $this->handleValues = $handleValues;
        $this->violationList = $violationList;
    }

    /**
     * @return HandleProperty[]
     */
    public function getHandleValues(): array
    {
        return $this->handleValues;
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
