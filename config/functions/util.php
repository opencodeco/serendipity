<?php

declare(strict_types=1);

namespace Serendipity\Type\Util;

if (! function_exists('extractArray')) {
    /**
     * @template T
     * @template U
     * @param array<string, array<T, U>> $array
     * @param array<T, U> $default
     * @return array<T, U>
     */
    function extractArray(array $array, string $property, array $default = []): array
    {
        $details = $array[$property] ?? null;
        if (! is_array($details)) {
            return $default;
        }
        return $details;
    }
}

if (! function_exists('extractString')) {
    /**
     * @param array<string, mixed> $array
     */
    function extractString(array $array, string $property, string $default = ''): string
    {
        $string = $array[$property] ?? $default;
        return is_string($string) ? $string : $default;
    }
}

if (! function_exists('extractInt')) {
    /**
     * @param array<string, mixed> $array
     */
    function extractInt(array $array, string $property, int $default = 0): int
    {
        $int = $array[$property] ?? $default;
        return is_int($int) ? $int : $default;
    }
}

if (! function_exists('extractBool')) {
    /**
     * @param array<string, mixed> $array
     */
    function extractBool(array $array, string $property, bool $default = false): bool
    {
        $bool = $array[$property] ?? $default;
        return is_bool($bool) ? $bool : $default;
    }
}

if (! function_exists('extractNumeric')) {
    /**
     * @param array<string, mixed> $array
     */
    function extractNumeric(array $array, string $property, float|int $default = 0): float
    {
        $numeric = $array[$property] ?? $default;
        return (float) (is_numeric($numeric) ? $numeric : $default);
    }
}
