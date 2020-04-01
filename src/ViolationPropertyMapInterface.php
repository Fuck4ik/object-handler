<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * A list of constraint violation property map.
 *
 * @author Roman Yastrebov <tirastwo@gmail.com>
 */
interface ViolationPropertyMapInterface extends \Traversable, \Countable, \ArrayAccess
{
    /**
     * Map an existing violation list into this property map.
     *
     * @param ConstraintViolationListInterface $violationList The violation list
     */
    public function map(ConstraintViolationListInterface $violationList);

    /**
     * Returns the violation at a given propertyPath.
     *
     * @param string $propertyPath The propertyPath of the violation
     *
     * @return ConstraintViolationListInterface|ViolationPropertyMapInterface $violationList The violation list or property map
     *
     * @throws \OutOfBoundsException if the propertyPath does not exist
     */
    public function get(string $propertyPath);

    /**
     * Returns whether the given propertyPath exists.
     *
     * @param string $propertyPath The violation propertyPath
     *
     * @return bool Whether the propertyPath exists
     */
    public function has(string $propertyPath): bool;

    /**
     * Sets a violation at a given propertyPath.
     *
     * @param string $propertyPath The violation propertyPath
     * @param ConstraintViolationListInterface|ViolationPropertyMapInterface $violationList The violation list or property map
     */
    public function set(string $propertyPath, $violationList);

    /**
     * Removes a violation at a given propertyPath.
     *
     * @param string $propertyPath The propertyPath to remove
     */
    public function remove(string $propertyPath);
}
