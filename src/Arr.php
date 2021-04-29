<?php

namespace Apfelfrisch\DataTransferObject;

use ArrayAccess;

class Arr
{
    /**
     * @param list<string> $keys
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * @param list<string> $keys
     */
    public static function except(array $array, array $keys): array
    {
        if (count($keys) === 0) {
            return $array;
        }

        foreach ($keys as $key) {
            if (static::exists($array, $key)) {
                unset($array[$key]);
                continue;
            }
        }

        return $array;
    }

    public static function exists(array $array, string $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }
}
