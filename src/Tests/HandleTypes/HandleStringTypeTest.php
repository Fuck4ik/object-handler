<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\HandleTypes;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\HandleTypes\HandleStringType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

class HandleStringTypeTest extends TestCase
{
    /**
     * @throws HandlerException
     * @throws \ReflectionException
     */
    public function testHandlingType(): void
    {
        $objectHandler = new ObjectHandler();
        $objectHandler->addHandleType(new HandleStringType());

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
            $object = new class {
                public string $test;
            };

            $violList = $objectHandler->handle($object, [
                'test' => $value['handle'],
            ]);

            $this->assertSame($violList->count(), 0);
            $this->assertSame($object->test, $value['wait']);
        }

        $invalidValues = [
            ['handle' => []],
        ];

        foreach ($invalidValues as $value) {
            $object = new class {
                public string $test;
            };

            $violList = $objectHandler->handle($object, [
                'test' => $value['handle'],
            ]);

            $this->assertTrue($violList->has('test'));
        }
    }
}