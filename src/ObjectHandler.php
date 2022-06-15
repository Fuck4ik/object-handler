<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Omasn\ObjectHandler\Exception\HandleTypeNotFoundException;
use Omasn\ObjectHandler\Exception\RequireArgumentException;
use Omasn\ObjectHandler\Exception\UnionTypeException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\Extractor\ConstructorDefaultValueExtractor;
use Omasn\ObjectHandler\Extractor\DefaultValueExtractorInterface;
use Omasn\ObjectHandler\Extractor\PropertyDefaultValueExtractor;
use Omasn\ObjectHandler\Helper\ArrayHelper;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\ConstraintViolationList;

final class ObjectHandler extends AbstractHandler
{
    /**
     * @var HandleTypeInterface[]
     */
    protected array $handleTypes = [];

    private PropertyInfoExtractorInterface $propertyInfoExtractor;
    private PropertyAccessorInterface $propertyAccessor;
    private ViolationFactoryInterface $violationFactory;

    public function __construct(
        PropertyInfoExtractorInterface $propertyInfoExtractor,
        PropertyAccessorInterface $propertyAccessor = null,
        ViolationFactoryInterface $violationFactory = null
    ) {
        $this->propertyInfoExtractor = $propertyInfoExtractor;
        $this->propertyAccessor = $propertyAccessor ?? new PropertyAccessor();
        $this->violationFactory = $violationFactory ?? new ViolationFactory();
        parent::__construct($violationFactory);
    }

    public function addHandleType(HandleTypeInterface $handleType): void
    {
        $this->handleTypes[$handleType->getId()] = $handleType;
    }

    /**
     * Создает новый экземпляр класса на основе переданных данных $data через конструктор
     * Использованные данные из $data удаляются через unset
     *
     * @param class-string $class
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
    ): object {
        $reflClass = new ReflectionClass($class);

        if (null === $constructor = $reflClass->getConstructor()) {
            return new $class();
        }

        if (!$constructor->isPublic()) {
            return $reflClass->newInstanceWithoutConstructor();
        }

        if (0 === $constructor->getNumberOfRequiredParameters()) {
            return new $class();
        }

        if ([] !== $data && !ArrayHelper::isAssoc($data)) {
            throw new RuntimeException('invalid data');
        }

        $context = $context ?? new HandleContext();
        $defaultValueExtractor = $defaultValueExtractor ?? new ConstructorDefaultValueExtractor();
        $violationList = new ConstraintViolationList();

        $dontResolved = [];
        $params = [];
        foreach ($constructor->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            $objProp = $this->createObjectProperty($class, $parameterName, $defaultValueExtractor);

            if (!array_key_exists($parameterName, $data)) {
                try {
                    if (!$this->ifSkippingHandle($objProp)) {
                        $params[$parameterName] = null;
                    }
                } catch (RuntimeException $e) {
                    $violationList->add($this->violationFactory->createNotBlank($parameterName));
                }

                continue;
            }

            $handleProperty = new HandleProperty($objProp, $data[$parameterName] ?? null);
            $this->resolveHandleProperty(
                $handleProperty,
                $violationList,
                $context
            );
            if ($handleProperty->isHandled()) {
                $params[$parameterName] = $handleProperty->getValue();
                unset($data[$parameterName]);
            } else {
                $dontResolved[] = $handleProperty;
            }
        }

        if (count($dontResolved) > 0) {
            throw new RequireArgumentException($dontResolved, $violationList);
        }

        if ($violationList->count() > 0) {
            throw new ViolationListException($violationList);
        }

        if ($constructor->isConstructor()) {
            return $reflClass->newInstanceArgs($params);
        }

        return $constructor->invokeArgs(null, $params);
    }

    /**
     * В переданный экземпляр класса устанавливаются данные из $data
     * По инструкции драйвера, работает с публичными свойствами и\или сеттерами
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
    ): void {
        $context = $context ?? new HandleContext();
        $defaultValueExtractor = $defaultValueExtractor ?? new PropertyDefaultValueExtractor();
        $violationList = new ConstraintViolationList();

        $objectClass = get_class($object);
        if (null === $properties = $this->propertyInfoExtractor->getProperties($objectClass)) {
            return;
        }

        foreach ($properties as $propertyName) {
            if (!$this->propertyAccessor->isWritable($object, $propertyName)) {
                continue;
            }

            $objProp = $this->createObjectProperty($objectClass, $propertyName, $defaultValueExtractor);

            if (!array_key_exists($propertyName, $data)) {
                try {
                    $ignoreMissing = $context->isIgnoreMissingData()
                        || (new ReflectionProperty($objectClass, $propertyName))->isInitialized($object);
                } catch (ReflectionException $e) {
                    $ignoreMissing = false;
                }
                if ($ignoreMissing) {
                    continue;
                }

                try {
                    if ($this->ifSkippingHandle($objProp)) {
                        continue;
                    }
                } catch (RuntimeException $e) {
                    $violationList->add($this->violationFactory->createNotBlank($propertyName));

                    continue;
                }
            }

            $handleProperty = new HandleProperty($objProp, $data[$propertyName] ?? null);
            $this->resolveHandleProperty(
                $handleProperty,
                $violationList,
                $context,
            );

            if ($handleProperty->isHandled()) {
                $this->propertyAccessor->setValue($object, $propertyName, $handleProperty->getValue());
            }
            unset($handleProperty);
        }

        if ($violationList->count() > 0) {
            throw new ViolationListException($violationList);
        }
    }

    /**
     * @return T
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
    ): object {
        $object = $this->instantiateObject($class, $data, $context, $defaultValueExtractor);
        $this->handleObject($object, $data, $context, $defaultValueExtractor);

        return $object;
    }

    /**
     * @throws HandleTypeNotFoundException
     */
    protected function getHandleType(HandleProperty $handleProperty): HandleTypeInterface
    {
        foreach ($this->handleTypes as $handleType) {
            if ($handleType->supports($handleProperty)) {
                return $handleType;
            }
        }

        $type = $handleProperty->getType()->getClassName() ?: $handleProperty->getType()->getBuiltinType();

        throw new HandleTypeNotFoundException($type);
    }

    private function ifSkippingHandle(ObjectProperty $objProp): bool
    {
        if ($objProp->isDefaultValue()) {
            return true;
        }

        if ($objProp->getType()->isNullable()) {
            return false;
        }

        throw new \RuntimeException('Property data required');
    }

    /**
     * @throws UnionTypeException
     */
    private function createObjectProperty(
        string $class,
        string $property,
        DefaultValueExtractorInterface $defaultValueExtractor
    ): ObjectProperty {
        return new ObjectProperty(
            $property,
            $this->getPropertyType($class, $property),
            $defaultValueExtractor->hasDefaultValue($class, $property)
        );
    }

    /**
     * @throws UnionTypeException
     */
    private function getPropertyType(string $class, string $property): Type
    {
        $types = $this->propertyInfoExtractor->getTypes($class, $property);

        if (null === $types || 0 === count($types)) {
            return self::getUndefinedType();
        }

        if (count($types) > 1) {
            throw new UnionTypeException($property, $class);
        }

        return $types[0];
    }
}
