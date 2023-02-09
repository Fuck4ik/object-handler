<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\Integration\Types;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleTypes\HandleDateTimeImmutableType;
use Omasn\ObjectHandler\ObjectHandler;
use Omasn\ObjectHandler\Tests\Integration\PropertyInfoTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HandleDateTimeImmutableTypeTest extends TestCase
{
    public function testOneSetPublic(): void
    {
        $objectHandler = ObjectHandler::createSimple([new HandleDateTimeImmutableType()]);

        $validValues = [
            [
                'handle' => '1970-01-01T00:00:00',
                'wait' => new \DateTimeImmutable('1970-01-01T00:00:00'),
            ],
            [
                'handle' => '1970-01-01',
                'wait' => new \DateTimeImmutable('1970-01-01'),
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                public \DateTimeImmutable $test;
            };

            $objectHandler->handleObject($object, [
                'test' => $value['handle'],
            ]);

            $this->assertEquals($object->test, $value['wait']);
        }

        $invalidValues = [
            ['handle' => '1970-99-99'],
            ['handle' => 'QWERTYqwerty'],
        ];

        foreach ($invalidValues as $value) {
            $object = new class() {
                public \DateTimeImmutable $test;
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
        $objectHandler = ObjectHandler::createSimple([new HandleDateTimeImmutableType()]);

        $validValues = [
            [
                'handle' => '1970-01-01T00:00:00',
                'wait' => new \DateTime('1970-01-01T00:00:00'),
            ],
            [
                'handle' => '1970-01-01',
                'wait' => new \DateTime('1970-01-01'),
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class() {
                private \DateTimeImmutable $test;

                public function setTest(\DateTimeImmutable $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): \DateTimeImmutable
                {
                    return $this->test;
                }
            };

            $objectHandler->handleObject($object, [
                'test' => $value['handle'],
            ]);

            $this->assertEquals($object->getTest(), $value['wait']);
        }

        $invalidValues = [
            ['handle' => '1970-99-99'],
            ['handle' => 'QWERTYqwerty'],
        ];

        foreach ($invalidValues as $value) {
            $object = new class() {
                private \DateTimeImmutable $test;

                public function setTest(\DateTimeImmutable $value): void
                {
                    $this->test = $value;
                }

                public function getTest(): \DateTimeImmutable
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
