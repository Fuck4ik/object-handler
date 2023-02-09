<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\Integration\Types;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleTypes\HandleBoolType;
use Omasn\ObjectHandler\ObjectHandler;
use Omasn\ObjectHandler\Tests\Integration\PropertyInfoTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * @internal
 * @coversNothing
 */
class HandleBoolTypeTest extends TestCase
{
    public function testOneSetPublic(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleBoolType()]);

        $validValues = [
            [
                'handle' => true,
                'wait' => true,
            ],
            [
                'handle' => 1,
                'wait' => true,
            ],
            [
                'handle' => 0,
                'wait' => false,
            ],
            [
                'handle' => '0',
                'wait' => false,
            ],
            [
                'handle' => 'false',
                'wait' => true,
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                public bool $test;
            };

            $objectHandler->handleObject($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($object->test, $value['wait']);
        }

        $invalidValues = [
            //            ['handle' => []],
        ];

        foreach ($invalidValues as $value) {
            $object = new class() {
                public bool $test;
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
        $objectHandler = ObjectHandler::createSimple([new HandleBoolType()]);

        $validValues = [
            [
                'handle' => true,
                'wait' => true,
            ],
            [
                'handle' => 1,
                'wait' => true,
            ],
            [
                'handle' => 0,
                'wait' => false,
            ],
            [
                'handle' => '0',
                'wait' => false,
            ],
            [
                'handle' => 'false',
                'wait' => true,
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                private bool $test;

                public function setTest(bool $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): bool
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
            //            ['handle' => []],
        ];

        foreach ($invalidValues as $value) {
            $object = new class() {
                private bool $test;

                public function setTest(bool $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): bool
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
