<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\HandleTypes;

use Omasn\ObjectHandler\Drivers\PublicPropertyDriver;
use Omasn\ObjectHandler\Drivers\SetMethodDriver;
use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\HandleTypes\HandleArrayType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

class HandleArrayTypeTest extends TestCase
{
    /**
     * @throws HandlerException
     * @throws \ReflectionException
     */
    public function testOneSetPublic(): void
    {
        $objectHandler = new ObjectHandler(new PublicPropertyDriver());
        $objectHandler->addHandleType(new HandleArrayType());

        $validValues = [
            [
                'handle' => [123],
                'wait' => [123],
            ],
            [
                'handle' => ['123'],
                'wait' => ['123'],
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class {
                public array $test;
            };

            $violList = $objectHandler->handle($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($violList->count(), 0);
            $this->assertSame($object->test, $value['wait']);
        }

        $invalidValues = [
//            ['handle' => new class{}],
        ];

        foreach ($invalidValues as $value) {
            $object = new class {
                public array $test;
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
        $objectHandler->addHandleType(new HandleArrayType());

        $validValues = [
            [
                'handle' => [123],
                'wait' => [123],
            ],
            [
                'handle' => ['123'],
                'wait' => ['123'],
            ],
        ];

        foreach ($validValues as $value) {
            $object = new class {
                private array $test;

                public function setTest(array $value): void {
                    $this->test = $value;
                }
                public function getTest(): array {
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
//            ['handle' => new class{}],
        ];

        foreach ($invalidValues as $value) {
            $object = new class {
                private array $test;

                public function setTest(array $value): void {
                    $this->test = $value;
                }
                public function getTest(): array {
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