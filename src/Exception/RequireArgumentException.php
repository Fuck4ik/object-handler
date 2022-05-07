<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Exception;

use Omasn\ObjectHandler\HandleProperty;

final class RequireArgumentException extends \Exception
{
    /**
     * @var HandleProperty[]
     */
    private array $handleValues;

    /**
     * @param HandleProperty[] $handleValues
     */
    public function __construct(array $handleValues)
    {
        parent::__construct('You cannot initialize an object through a constructor without these arguments.');
        $this->handleValues = $handleValues;
    }

    /**
     * @return HandleProperty[]
     */
    public function getHandleValues(): array
    {
        return $this->handleValues;
    }
}
