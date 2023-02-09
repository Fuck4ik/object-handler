<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\Integration\NewInstance;

use Omasn\ObjectHandler\Exception\HandleTypeNotFoundException;
use Omasn\ObjectHandler\Exception\RequireArgumentException;
use Omasn\ObjectHandler\Exception\UnionTypeException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleTypes\HandleArrayType;
use Omasn\ObjectHandler\HandleTypes\HandleIntType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;

/**
 * @internal
 * @coversNothing
 */
final class BaseFunctionalTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ViolationListException
     * @throws HandleTypeNotFoundException
     * @throws RequireArgumentException
     * @throws UnionTypeException
     */
    public function testOneSetPublic(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleArrayType()]);

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
     * @throws ReflectionException
     * @throws ViolationListException
     * @throws HandleTypeNotFoundException
     * @throws RequireArgumentException
     * @throws UnionTypeException
     */
    public function testExecConstructorCalc(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleArrayType(), new HandleIntType()]);

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
     * @throws ReflectionException
     * @throws ViolationListException
     * @throws HandleTypeNotFoundException
     * @throws RequireArgumentException
     * @throws UnionTypeException
     */
    public function testInvalidData(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleIntType()]);

        $data2 = ['not assoc'];
        $this->expectException(RuntimeException::class);
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
