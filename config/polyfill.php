<?php

declare(strict_types=1);

use function Serendipity\Type\Cast\toArray;
use function Serendipity\Type\Cast\toInt;
use function Serendipity\Type\Util\extractInt;

if (! function_exists('array_flatten')) {
    function array_flatten(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result += array_flatten($value);
                continue;
            }
            $result[$key] = $value;
        }
        return $result;
    }
}

if (! function_exists('array_flatten_prefixed')) {
    function array_flatten_prefixed(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result += array_flatten_prefixed($value, $prefix . $key . '.');
                continue;
            }
            $result[$prefix . $key] = $value;
        }
        return $result;
    }
}

if (! function_exists('array_shift_pluck_int')) {
    function array_shift_pluck_int(mixed $result, string $property): ?int
    {
        $data = toArray($result);
        if (empty($data)) {
            return null;
        }
        $datum = toArray($data[0]);
        $id = toInt(extractInt($datum, $property));
        return $id === 0 ? null : $id;
    }
}
