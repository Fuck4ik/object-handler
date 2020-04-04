<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Drivers;

use Omasn\ObjectHandler\Exception\MethodNotFoundException;
use Omasn\ObjectHandler\HandleDriverInterface;

class SetMethodDriver implements HandleDriverInterface
{
    /**
     * @inheritDoc
     */
    public function getPropertyFilters(): int
    {
        return \ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC;
    }

    /**
     * @inheritDoc
     *
     * @throws MethodNotFoundException
     * @throws \ReflectionException
     */
    public function setPropertyValue($object, \ReflectionProperty $property, $value): void
    {
        $method = 'set' . $this->camelize($property->getName());
        $this->checkMethod($object, $method);

        $object->$method($value);
    }


    /**
     * Check exist set method
     *
     * @param object $object
     * @param string $method
     * @return void
     *
     * @throws MethodNotFoundException
     * @throws \ReflectionException
     */
    private function checkMethod(object $object, string $method): void
    {
        $reflector = new \ReflectionClass($object);

        if ($reflector->hasMethod($method) && $reflector->getMethod($method)->isPublic()) {
            return;
        }

        if ($reflector->hasMethod('__call') && $reflector->getMethod('__call')->isPublic()) {
            return;
        }

        throw new MethodNotFoundException(sprintf('Method "%s" or "__call" not found in class "%s"', $method, $reflector->getName()));
    }


    /**
     * Camelize a given string.
     *
     * @param string $string
     * @return string
     */
    private function camelize(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}