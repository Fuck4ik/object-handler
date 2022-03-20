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

use Omasn\ObjectHandler\HandleTypes\HandleBoolType;
use Omasn\ObjectHandler\HandleTypes\HandleIntType;
use Omasn\ObjectHandler\HandleTypes\HandleStringType;
use Omasn\ObjectHandler\ObjectHandler;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

$phpDocExtractor = new PhpDocExtractor();
$reflectionExtractor = new ReflectionExtractor();

$propertyInfoExtractor = new PropertyInfoExtractor(
    [$reflectionExtractor],
    [$phpDocExtractor, $reflectionExtractor],
    [],
    [$reflectionExtractor],
    [$reflectionExtractor]
);

// create a object handler and configure project handle types
$objectHandler = new ObjectHandler($propertyInfoExtractor);
$objectHandler->addHandleType(new HandleStringType());
$objectHandler->addHandleType(new HandleIntType());
$objectHandler->addHandleType(new HandleBoolType());

$object = new class {
    public string $text;
    public int $count;
    public bool $active;
};

try {
    $objectHandler->handleObject($object, [
        'text' => 123,
        'count' => '5',
        'active' => 0,
    ]);
} catch (\Omasn\ObjectHandler\Exception\ViolationListException $e) {
    $e->getViolationList()->count(); // Count handle validation errors
}

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

Roman Yastrebov (Telegram: <https://t.me/omasn>)

### License

Object handler is licensed under the MIT License - see the `LICENSE` file for details