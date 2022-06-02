<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Omasn\ObjectHandler\Exception\RequireArgumentException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\Extractor\DefaultValueExtractorInterface;
use ReflectionException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ObjectHandlerInterface
{
    public function addHandleType(HandleTypeInterface $handleType): void;

    /**
     * Creates a new instance of the class based on the $data passed through the constructor
     * The passed instance of the class is set (implemented HandleType) to the data from $data
     * According symfony/property-info instructions
     *
     * @throws HandlerException
     * @throws ReflectionException
     * @throws ViolationListException
     *
     * @template T
     * @psalm-param class-string<T> $class
     *
     * @return T
     */
    public function handle(
        string $class,
        array $data,
        HandleContextInterface $context = null,
        DefaultValueExtractorInterface $defaultValueExtractor = null
    );

    /**
     * Creates a new instance of the class based on the $data passed through the constructor
     * Used data from $data is removed via unset
     *
     * @param class-string $class
     *
     * @throws HandlerException
     * @throws ReflectionException
     * @throws ViolationListException
     * @throws RequireArgumentException
     */
    public function instantiateObject(
        string $class,
        array &$data,
        HandleContextInterface $context = null,
        DefaultValueExtractorInterface $defaultValueExtractor = null
    ): object;

    /**
     * The passed instance of the class is set (implemented HandleType) to the data from $data
     * According symfony/property-info instructions
     *
     * @throws HandlerException
     * @throws ViolationListException
     */
    public function handleObject(
        object $object,
        array $data,
        HandleContextInterface $context = null,
        DefaultValueExtractorInterface $defaultValueExtractor = null
    ): void;

    /**
     * @throws HandlerException
     * @throws ObjectHandlerException
     * @throws ViolationListException
     */
    public function handleProperty(HandleProperty $handleProperty, HandleContextInterface $context): HandleProperty;

    /**
     * @throws HandlerException
     */
    public function resolveHandleProperty(
        HandleProperty $handleProperty,
        ConstraintViolationListInterface $violationList,
        HandleContextInterface $context
    ): void;
}
