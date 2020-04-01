<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\HandleTypes;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\HandleTypes\HandleFloatType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

class HandleFloatTypeTest extends TestCase
{
    /**
     * @throws HandlerException
     * @throws \ReflectionException
     */
    public function testHandlingType(): void
    {
        $objectHandler = new ObjectHandler();
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
}