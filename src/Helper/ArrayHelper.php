<?php

declare(strict_types=1);

namespace Omasn\ObjectHandler\Helper;

final class ArrayHelper
{
    public static function isAssoc(array $arr): bool
    {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
