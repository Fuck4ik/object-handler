<?php declare(strict_types=1);

namespace Omasn\ObjectHandler;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Default implementation of {@ViolationPropertyMapInterface}.
 *
 * @author Roman Yastrebov <tirastwo@gmail.com>
 */
class ViolationPropertyMap implements \IteratorAggregate, ViolationPropertyMapInterface
{
    /**
     * @var ConstraintViolationListInterface[]
     */
    private array $propertyMap = [];

    /**
     * Map a new constraint violation property.
     *
     * @param ConstraintViolationListInterface|ConstraintViolationInterface[] $violationList
     */
    public function map(ConstraintViolationListInterface $violationList)
    {
        foreach ($violationList as $violation) {
            if ($this->has($violation->getPropertyPath())) {
                $propertyViolationList = $this->get($violation->getPropertyPath());
                $propertyViolationList->add($violation);
            } else {
                $this->set($violation->getPropertyPath(), new ConstraintViolationList([$violation]));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $propertyPath)
    {
        if (!$this->has($propertyPath)) {
            throw new \OutOfBoundsException(sprintf('The offset "%s" does not exist.', $propertyPath));
        }

        return $this->propertyMap[$propertyPath];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $propertyPath): bool
    {
        return isset($this->violations[$propertyPath]);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $propertyPath, $violationList)
    {
        $this->propertyMap[$propertyPath] = $violationList;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $propertyPath)
    {
        unset($this->propertyMap[$propertyPath]);
    }

    /**
     * {@inheritdoc}
     *
     * @return \ArrayIterator|ConstraintViolationListInterface[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->propertyMap);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->propertyMap);
    }

    /**
     * @param $propertyPath
     * @return bool
     */
    public function offsetExists($propertyPath): bool
    {
        return $this->has($propertyPath);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($propertyPath)
    {
        return $this->get($propertyPath);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($propertyPath, $violation)
    {
        if (null === $propertyPath) {
            throw new \OutOfBoundsException('Property path required.');
        }

        $this->set($propertyPath, $violation);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($propertyPath)
    {
        $this->remove($propertyPath);
    }
}
