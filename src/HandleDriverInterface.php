<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler;

/**
 * Driver for work Object Handler.
 *
 * @author Roman Yastrebov <tirastwo@gmail.com>
 */
interface HandleDriverInterface
{
    public function supportProperty(\ReflectionProperty $property): bool;

    public function supportMethod(\ReflectionMethod $method): bool;

    /**
     * If the property is non-static an object must be provided to change
     * the property on. If the property is static this parameter is left
     * out and only <i>value</i> needs to be provided.
     *
     * @param $object object Handle object
     * @param $value
     */
    public function setPropertyValue($object, ObjectProperty $property, $value): void;

    public function extractPropertyNameFromMethod(\ReflectionMethod $method): string;
}
