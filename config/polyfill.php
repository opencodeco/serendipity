<?php

declare(strict_types=1);

if (! function_exists('array_flatten')) {
    function array_flatten(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result += array_flatten($value, $prefix . $key . '.');
                continue;
            }
            $result[$prefix . $key] = $value;
        }
        return $result;
    }
}
