<?php

declare(strict_types=1);

namespace Serendipity\Type\Cast;

if (! function_exists('arrayify')) {
    /**
     * @template T of array-key
     * @template U
     * @param array<T, U> $default
     * @return array<T, U>
     */
    function arrayify(mixed $value, array $default = []): array
    {
        return is_array($value) ? $value : $default;
    }
}

if (! function_exists('stringify')) {
    function stringify(mixed $value, string $default = ''): string
    {
        return is_string($value) ? $value : $default;
    }
}

if (! function_exists('integerify')) {
    function integerify(mixed $value, int $default = 0): int
    {
        $value = is_scalar($value) ? (int) $value : $value;
        return is_int($value) ? $value : $default;
    }
}

if (! function_exists('floatify')) {
    function floatify(mixed $value, float $default = 0.0): float
    {
        return is_float($value) ? $value : $default;
    }
}

if (! function_exists('boolify')) {
    function boolify(mixed $value, bool $default = false): bool
    {
        return is_bool($value) ? $value : $default;
    }
}

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

namespace Serendipity\Type\String;

use function Serendipity\Type\Cast\stringify;

if (! function_exists('snakify')) {
    function snakify(string $string): string
    {
        $string = stringify(preg_replace('/[A-Z]/', '_$0', $string));
        return strtolower(ltrim($string, '_'));
    }
}

namespace Serendipity\Type\Json;

use JsonException;

use function Serendipity\Type\Cast\arrayify;
use function Serendipity\Type\Cast\stringify;

if (! function_exists('decode')) {
    function decode(string $json): ?array
    {
        try {
            return arrayify(json_decode($json, true, 512, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            return null;
        }
    }
}

if (! function_exists('encode')) {
    function encode(array $data): ?string
    {
        try {
            return stringify(json_encode($data, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            return null;
        }
    }
}

namespace Serendipity\Coroutine;

use Hyperf\Coroutine\Coroutine;

if (! function_exists('coroutine')) {
    function coroutine(callable $callback): int
    {
        return Coroutine::create($callback);
    }
}
