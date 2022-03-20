<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ViolationListException extends \Exception
{
    private ConstraintViolationListInterface $violationList;

    public function __construct(ConstraintViolationListInterface $violationList)
    {
        parent::__construct('Object handle violation list exception');
        $this->violationList = $violationList;
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
