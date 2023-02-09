<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\Integration\Types;

use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleTypes\HandleStringType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HandleStringTypeTest extends TestCase
{
    public function testOneSetPublic(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleStringType()]);

        $validValues = [
            [
                'handle' => 'QWERTYqwerty',
                'wait' => 'QWERTYqwerty',
            ],
            [
                'handle' => 123,
                'wait' => '123',
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                public string $test;
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
                public string $test;
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
        $objectHandler = ObjectHandler::createSimple([new HandleStringType()]);

        $validValues = [
            [
                'handle' => 'QWERTYqwerty',
                'wait' => 'QWERTYqwerty',
            ],
            [
                'handle' => 123,
                'wait' => '123',
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                private string $test;

                public function setTest(string $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): string
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
                private string $test;

                public function setTest(string $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): string
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
