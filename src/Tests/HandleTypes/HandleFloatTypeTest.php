<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\HandleTypes;

use Omasn\ObjectHandler\Drivers\PublicPropertyDriver;
use Omasn\ObjectHandler\Drivers\SetMethodDriver;
use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\HandleTypes\HandleFloatType;
use Omasn\ObjectHandler\HandleTypes\HandleStringType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

class HandleFloatTypeTest extends TestCase
{
    /**
     * @throws HandlerException
     * @throws \ReflectionException
     */
    public function testOneSetPublic(): void
    {
        $objectHandler = new ObjectHandler(new PublicPropertyDriver());
        $objectHandler->addHandleType(new HandleFloatType());

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
                'wait' => 0.0
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class {
                public float $test;
            };

            $violList = $objectHandler->handle($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($violList->count(), 0);
            $this->assertSame($object->test, $value['wait']);
        }

        $invalidValues = [
//            ['handle' => []],
        ];

        foreach ($invalidValues as $value) {
            $object = new class {
                public float $test;
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
        $objectHandler->addHandleType(new HandleFloatType());

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
                'wait' => 0.0
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class {
                private float $test;

                public function setTest(float $value): void {
                    $this->test = $value;
                }
                public function getTest(): float {
                    return $this->test;
                }
            };

            $violList = $objectHandler->handle($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($violList->count(), 0);
            $this->assertSame($object->getTest(), $value['wait']);
        }

        $invalidValues = [
//            ['handle' => []],
        ];

        foreach ($invalidValues as $value) {
            $object = new class {
                private float $test;

                public function setTest(float $value): void {
                    $this->test = $value;
                }
                public function getTest(): float {
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