<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

/**
 * Driver for work Object Handler.
 *
 * @author Roman Yastrebov <tirastwo@gmail.com>
 */
interface HandleDriverInterface
{
    /**
     * @link https://php.net/manual/en/reflectionclass.getproperties.php
     *
     * The optional filter, for filtering desired property types. It's configured using
     * the ReflectionProperty constants,
     * and defaults to all property types.
     */
    public function getPropertyFilters(): int;

    /**
     * If the property is non-static an object must be provided to change
     * the property on. If the property is static this parameter is left
     * out and only <i>value</i> needs to be provided.
     *
     * @param $object object Handle object
     * @param \ReflectionProperty $property
     * @param $value
     */
    public function setPropertyValue($object, \ReflectionProperty $property, $value): void;
}
