<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\Integration\Types;

use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleTypes\HandleIntType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HandleIntTypeTest extends TestCase
{
    public function testOneSetPublic(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleIntType()]);

        $validValues = [
            [
                'handle' => 123,
                'wait' => 123,
            ],
            [
                'handle' => '123',
                'wait' => 123,
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                public int $test;
            };

            $objectHandler->handleObject($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($object->test, $value['wait']);
        }

        $invalidValues = [
            ['handle' => []],
            ['handle' => 'QWERTYqwerty'],
        ];

        foreach ($invalidValues as $value) {
            $object = new class() {
                public int $test;
            };

            try {
                $objectHandler->handleObject($object, [
                    'test' => $value['handle'],
                ]);
                $this->fail(sprintf('Dont excepted exception %s', ViolationListException::class));
            } catch (ViolationListException $e) {
                $this->assertSame($e->getViolationList()->count(), 1);
            }
        }
    }

    public function testOneSetMethod(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleIntType()]);

        $validValues = [
            [
                'handle' => 123,
                'wait' => 123,
            ],
            [
                'handle' => '123',
                'wait' => 123,
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                public int $test;

                public function setTest(int $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): int
                {
                    return $this->test;
                }
            };

            $objectHandler->handleObject($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($object->test, $value['wait']);
        }

        $invalidValues = [
            ['handle' => []],
            ['handle' => 'QWERTYqwerty'],
        ];

        foreach ($invalidValues as $value) {
            $object = new class() {
                public int $test;

                public function setTest(int $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): int
                {
                    return $this->test;
                }
            };

            try {
                $objectHandler->handleObject($object, [
                    'test' => $value['handle'],
                ]);
                $this->fail(sprintf('Dont excepted exception %s', ViolationListException::class));
            } catch (ViolationListException $e) {
                $this->assertSame($e->getViolationList()->count(), 1);
            }
        }
    }
}
