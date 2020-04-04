<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\HandleTypes;

use Omasn\ObjectHandler\Drivers\PublicPropertyDriver;
use Omasn\ObjectHandler\Drivers\SetMethodDriver;
use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\HandleTypes\HandleDateTimeInterfaceType;
use Omasn\ObjectHandler\HandleTypes\HandleStringType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

class HandleDateTimeInterfaceTypeTest extends TestCase
{
    /**
     * @throws HandlerException
     * @throws \ReflectionException
     */
    public function testOneSetPublic(): void
    {
        $objectHandler = new ObjectHandler(new PublicPropertyDriver());
        $objectHandler->addHandleType(new HandleDateTimeInterfaceType());

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
            $object = new class {
                public \DateTimeInterface $test;
            };

            $violList = $objectHandler->handle($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($violList->count(), 0);
            $this->assertEquals($object->test, $value['wait']);
        }

        $invalidValues = [
            ['handle' => '1970-99-99'],
            ['handle' => 'QWERTYqwerty'],
        ];

        foreach ($invalidValues as $value) {
            $object = new class {
                public \DateTimeInterface $test;
            };

            $violList = $objectHandler->handle($object, [
                'test' => $value['handle'],
            ]);

            $this->assertTrue($violList->has('test'));
        }
    }

    /**
     * @throws HandlerException
     * @throws \ReflectionException
     */
    public function testOneSetMethod(): void
    {
        $objectHandler = new ObjectHandler(new SetMethodDriver());
        $objectHandler->addHandleType(new HandleDateTimeInterfaceType());

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
            $object = new class {
                private \DateTimeInterface $test;

                public function setTest(\DateTimeInterface $value): void {
                    $this->test = $value;
                }
                public function getTest(): \DateTimeInterface {
                    return $this->test;
                }
            };

            $violList = $objectHandler->handle($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($violList->count(), 0);
            $this->assertEquals($object->getTest(), $value['wait']);
        }

        $invalidValues = [
            ['handle' => '1970-99-99'],
            ['handle' => 'QWERTYqwerty'],
        ];

        foreach ($invalidValues as $value) {
            $object = new class {
                private \DateTimeInterface $test;

                public function setTest(\DateTimeInterface $value): void {
                    $this->test = $value;
                }
                public function getTest(): \DateTimeInterface {
                    return $this->test;
                }
            };

            $violList = $objectHandler->handle($object, [
                'test' => $value['handle'],
            ]);

            $this->assertTrue($violList->has('test'));
        }
    }
}