# PHP - Object Handler

The component allows filling objects from an associative array
based on class strict property maps

## Installation

Install the latest version with

```bash
$ composer require omasn/object-handler
```

## Basic Usage

```php
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
```

## About

### Author

Roman Yastrebov - <tirastwo@gmail.com> - <https://www.instagram.com/omasn.hawk/>

### License

Object handler is licensed under the MIT License - see the `LICENSE` file for details