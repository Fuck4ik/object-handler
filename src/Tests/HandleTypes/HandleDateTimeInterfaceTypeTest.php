<?php declare(strict_types=1);

namespace Omasn\ObjectHandler\Tests\HandleTypes;

use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\HandleTypes\HandleDateTimeInterfaceType;
use Omasn\ObjectHandler\ObjectHandler;
use PHPUnit\Framework\TestCase;

class HandleDateTimeInterfaceTypeTest extends TestCase
{
    /**
     * @throws HandlerException
     * @throws \ReflectionException
     */
    public function testHandlingType(): void
    {
        $objectHandler = new ObjectHandler();
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
}