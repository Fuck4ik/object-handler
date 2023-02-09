<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandleTypeNotFoundException;
use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Omasn\ObjectHandler\Exception\RequireArgumentException;
use Omasn\ObjectHandler\Exception\UnionTypeException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\Extractor\DefaultValueExtractorInterface;
use ReflectionException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ObjectHandlerInterface
{
    /**
     * Creates a new instance of the class based on the $data passed through the constructor
     * The passed instance of the class is set (implemented HandleType) to the data from $data
     * According symfony/property-info instructions
     *
     * @return T
     * @throws ViolationListException
     *
     * @template T
     * @psalm-param class-string<T> $class
     *
     * @throws ViolationListException
     * @throws RequireArgumentException
     * @throws ReflectionException
     * @throws HandleTypeNotFoundException
     * @throws UnionTypeException
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
     * @template T
     * @psalm-param class-string<T> $class
     * @param class-string $class
     * @return T
     *
     * @throws ReflectionException
     * @throws ViolationListException
     * @throws RequireArgumentException
     * @throws HandleTypeNotFoundException
     * @throws UnionTypeException
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
     * @throws ViolationListException
     * @throws HandleTypeNotFoundException
     * @throws UnionTypeException
     */
    public function handleObject(
        object $object,
        array $data,
        HandleContextInterface $context = null,
        DefaultValueExtractorInterface $defaultValueExtractor = null
    ): void;

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
    ): HandleProperty;

    /**
     * @param class-string|null $class
     *
     * @throws HandleTypeNotFoundException
     */
    public function resolveHandleProperty(
        ?string $class,
        HandleProperty $handleProperty,
        ConstraintViolationListInterface $violationList,
        HandleContextInterface $context
    ): void;
}
