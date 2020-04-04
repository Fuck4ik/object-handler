<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\HandleTypes;

use Omasn\ObjectHandler\Drivers\PublicPropertyDriver;
use Omasn\ObjectHandler\Drivers\SetMethodDriver;
use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\HandleTypes\HandleBoolType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

class HandleBoolTypeTest extends TestCase
{
    /**
     * @throws HandlerException
     * @throws \ReflectionException
     */
    public function testOneSetPublic(): void
    {
        $objectHandler = new ObjectHandler(new PublicPropertyDriver());
        $objectHandler->addHandleType(new HandleBoolType());

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
            $object = new class {
                public bool $test;
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
                public bool $test;
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
        $objectHandler->addHandleType(new HandleBoolType());

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
            $object = new class {
                private bool $test;

                public function setTest(bool $value): void {
                    $this->test = $value;
                }
                public function getTest(): bool {
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
                private bool $test;

                public function setTest(bool $value): void {
                    $this->test = $value;
                }
                public function getTest(): bool {
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