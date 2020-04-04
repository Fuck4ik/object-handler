<?php

use Omasn\ObjectHandler\Drivers\PublicPropertyDriver;
use Omasn\ObjectHandler\HandleTypes\HandleBoolType;
use Omasn\ObjectHandler\HandleTypes\HandleIntType;
use Omasn\ObjectHandler\HandleTypes\HandleStringType;
use Omasn\ObjectHandler\ObjectHandler;

// create handle driver
$driver = new PublicPropertyDriver();

// create a object handler and configure project handle types
$objectHandler = new ObjectHandler($driver);
$objectHandler->addHandleType(new HandleStringType());
$objectHandler->addHandleType(new HandleIntType());
$objectHandler->addHandleType(new HandleBoolType());

$object = new class {
    public string $text;
    public int $count;
    public bool $active;
};

$violationsMap = $objectHandler->handle($object, [
    'text' => 123,
    'count' => '5',
    'active' => 0,
]);

$violationsMap->count(); // Count handle validation errors

var_dump($object);
// object(class@anonymous)#277 (3) {
//     ["text"]=>
//     string(3) "123"
//     ["count"]=>
//     int(5)
//     ["active"]=>
//     bool(false)
//   }