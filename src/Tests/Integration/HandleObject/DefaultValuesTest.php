<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\Integration\HandleObject;

use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleContext;
use Omasn\ObjectHandler\HandleTypes\HandleIntType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class DefaultValuesTest extends TestCase
{
    public function testSuccess(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleIntType()]);

        $object = new class() {
            public int $test1;
            public int $test2 = 3;
            public ?int $test3 = null;
            public ?int $test4;
        };

        $objectHandler->handleObject($object, [
            'test1' => '1',
            'test2' => '2',
        ]);

        $this->assertSame($object->test1, 1);
        $this->assertSame($object->test2, 2);
        $this->assertNull($object->test3);
        $this->assertFalse(isset($object->test4));
    }

    public function testFailed(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleIntType()]);

        $object = new class() {
            public int $test1; // valid
            public int $test2; // error
            public ?int $test3; // valid
            public ?int $test4 = null; // valid
        };

        try {
            $objectHandler->handleObject($object, [
                'test1' => '1',
            ], new HandleContext(false));
            $this->fail(sprintf('Dont excepted exception %s', ViolationListException::class));
        } catch (ViolationListException $e) {
            $this->assertSame($e->getViolationList()->count(), 1);
        }

        $this->assertSame($object->test1, 1);
        $this->assertFalse(isset($object->test2));
        $this->assertNull($object->test3);
        $this->assertNull($object->test4);
    }
}
