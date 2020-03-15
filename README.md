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

use Omasn\ObjectHandler\HandleTypes\HandleStringType;
use Omasn\ObjectHandler\ObjectHandler;

// create a object handler
$objectHandler = new ObjectHandler();
$objectHandler->addHandleType(new HandleStringType());


$object = new class {
    public string $test;
};

$violList = $objectHandler->handle($object, [
    'test' => 123,
]);

echo $object->test;
// '123'
```

## About

### Author

Roman Yastrebov - <tirastwo@gmail.com> - <https://www.instagram.com/omasn.hawk/>

### License

Monolog is licensed under the MIT License - see the `LICENSE` file for details