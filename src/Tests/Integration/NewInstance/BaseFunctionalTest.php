<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\Integration\NewInstance;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleTypes\HandleArrayType;
use Omasn\ObjectHandler\HandleTypes\HandleIntType;
use Omasn\ObjectHandler\ObjectHandler;
use Omasn\ObjectHandler\Tests\Integration\PropertyInfoTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * @internal
 * @coversNothing
 */
final class BaseFunctionalTest extends TestCase
{
    use PropertyInfoTrait;

    /**
     * @throws ViolationListException
     * @throws HandlerException
     * @throws ReflectionException
     */
    public function testOneSetPublic(): void
    {
        $objectHandler = new ObjectHandler($this->getPropertyInfo());
        $objectHandler->addHandleType(new HandleArrayType());

        $validValues = [
            [
                'handle' => [123],
                'wait' => [123],
            ],
            [
                'handle' => ['123'],
                'wait' => ['123'],
            ],
        ];

        foreach ($validValues as $value) {
            $data = [
                'test' => $value['handle'],
                'test2' => 'unused', // ignored
            ];

            $object = $objectHandler->instantiateObject(ArrayTest1::class, $data);

            $this->assertSame($object->test, $value['wait']);
            $this->assertArrayNotHasKey('test', $data);
        }
    }

    /**
     * @throws ViolationListException
     * @throws HandlerException
     * @throws ReflectionException
     */
    public function testExecConstructorCalc(): void
    {
        $objectHandler = new ObjectHandler($this->getPropertyInfo());
        $objectHandler->addHandleType(new HandleArrayType());
        $objectHandler->addHandleType(new HandleIntType());

        $validValues = [
            [
                'handle' => [1, 1],
                'handle2' => 1,
                'wait' => 4,
            ],
            [
                'handle' => ['1', 1],
                'handle2' => '1',
                'wait' => 4,
            ],
        ];

        foreach ($validValues as $value) {
            $data = [
                'test' => $value['handle'],
                'test2' => $value['handle2'],
            ];

            $object = $objectHandler->instantiateObject(ArrayTest2::class, $data);

            $this->assertSame($object->calc, $value['wait']);
        }
    }

    /**
     * @throws ViolationListException
     * @throws HandlerException
     * @throws ReflectionException
     */
    public function testInvalidData(): void
    {
        $objectHandler = new ObjectHandler($this->getPropertyInfo());
        $objectHandler->addHandleType(new HandleIntType());

        $data1 = [];
        $this->expectException(\RuntimeException::class);
        $objectHandler->instantiateObject(ArrayTest1::class, $data1);

        $data2 = ['not assoc'];
        $this->expectException(\RuntimeException::class);
        $objectHandler->instantiateObject(ArrayTest1::class, $data2);
    }
}

// Test classes
final class ArrayTest1
{
    public array $test;

    public function __construct(array $test)
    {
        $this->test = $test;
    }
}

// Test classes
final class ArrayTest2
{
    public int $calc = 1;

    public function __construct(array $test, int $test2)
    {
        $this->calc += array_sum($test) + $test2;
    }
}
