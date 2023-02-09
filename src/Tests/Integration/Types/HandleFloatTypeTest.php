<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\Integration\Types;

use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleTypes\HandleFloatType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HandleFloatTypeTest extends TestCase
{
    public function testOneSetPublic(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleFloatType()]);

        $validValues = [
            [
                'handle' => 1.1,
                'wait' => 1.1,
            ],
            [
                'handle' => 1,
                'wait' => 1.0,
            ],
            [
                'handle' => '1.2',
                'wait' => 1.2,
            ],
            [
                'handle' => 0,
                'wait' => 0.0,
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                public float $test;
            };

            $objectHandler->handleObject($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($object->test, $value['wait']);
        }

        $invalidValues = [
            ['handle' => []],
        ];

        foreach ($invalidValues as $value) {
            $object = new class() {
                public float $test;
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
        $objectHandler = ObjectHandler::createSimple([new HandleFloatType()]);

        $validValues = [
            [
                'handle' => 1.1,
                'wait' => 1.1,
            ],
            [
                'handle' => 1,
                'wait' => 1.0,
            ],
            [
                'handle' => '1.2',
                'wait' => 1.2,
            ],
            [
                'handle' => 0,
                'wait' => 0.0,
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                private float $test;

                public function setTest(float $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): float
                {
                    return $this->test;
                }
            };

            $objectHandler->handleObject($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($object->getTest(), $value['wait']);
        }

        $invalidValues = [
            ['handle' => []],
        ];

        foreach ($invalidValues as $value) {
            $object = new class() {
                private float $test;

                public function setTest(float $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): float
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
