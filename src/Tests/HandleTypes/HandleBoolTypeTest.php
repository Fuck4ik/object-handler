<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\HandleTypes;

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
    public function testHandlingType(): void
    {
        $objectHandler = new ObjectHandler();
        $objectHandler->addHandleType(new HandleBoolType());

        $validValues = [
//            [
//                'handle' => true,
//                'wait' => true,
//            ],
//            [
//                'handle' => 1,
//                'wait' => true,
//            ],
//            [
//                'handle' => 0,
//                'wait' => false,
//            ],
//            [
//                'handle' => '0',
//                'wait' => true,
//            ],
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

            $this->assertArrayHasKey(0, $violList);
            $this->assertSame($violList->get(0)->getPropertyPath(), 'test');
        }
    }
}