<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\Integration\Types;

use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\HandleTypes\HandleArrayIterationType;
use Omasn\ObjectHandler\HandleTypes\HandleIntType;
use Omasn\ObjectHandler\HandleTypes\HandleRecursiveType;
use Omasn\ObjectHandler\ObjectHandler;
use Omasn\ObjectHandler\ObjectHandlerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HandleRecursiveTypeTest extends TestCase
{
    public function testSuccessCollection(): void
    {
        $objectHandler = ObjectHandler::createSimple([
            function(ObjectHandlerInterface $objectHandler) {
                return new HandleRecursiveType(
                    $objectHandler,
                    fn(HandleProperty $property) => null !== $property->getType()->getClassName()
                );
            },
            new HandleIntType(),
            function(ObjectHandlerInterface $objectHandler) {
                return new HandleArrayIterationType($objectHandler);
            },
        ]);

        $object = $objectHandler->handle(TestA::class, [
            'testB' => [
                'collectionTests' => [
                    ['number' => '1', 'testD' => ['number2' => '11']],
                    ['number' => 2, 'testD' => ['number2' => 22]],
                ],
            ],
        ]);

        $this->assertSame($object->testB->collectionTests[0]->number, 1);
        $this->assertSame($object->testB->collectionTests[0]->testD->number2, 11);
        $this->assertSame($object->testB->collectionTests[1]->number, 2);
        $this->assertSame($object->testB->collectionTests[1]->testD->number2, 22);
    }
}

// Test classes
final class TestA
{
    public TestB $testB;
}

final class TestB
{
    /** @var TestC[] */
    public iterable $collectionTests;
}

final class TestC
{
    public int $number;
    public TestD $testD;
}

final class TestD
{
    public ?int $number2;
}
