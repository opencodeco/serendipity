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
        return match (true) {
            is_string($value) => $value,
            is_scalar($value) => (string) $value,
            (! is_object($value) && settype($value, 'string') !== false) => (string) $value,
            (is_object($value) && method_exists($value, '__toString')) => (string) $value,
            default => $default,
        };
    }
}

if (! function_exists('integerify')) {
    function integerify(mixed $value, int $default = 0): int
    {
        $value = is_numeric($value) ? (int) $value : $value;
        return is_int($value) ? $value : $default;
    }
}

if (! function_exists('floatify')) {
    function floatify(mixed $value, float $default = 0.0): float
    {
        $value = is_numeric($value) ? (float) $value : $value;
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

namespace Serendipity\Crypt;

use InvalidArgumentException;
use Serendipity\Domain\Support\Set;

use function Hyperf\Support\env;

if (! defined('DEFAULT_CRYPT_KEY')) {
    define('DEFAULT_CRYPT_KEY', base64_encode(random_bytes(32)));
}

if (! function_exists('encrypt')) {
    function encrypt(string $plaintext): string
    {
        $key = env('CRYPT_KEY', DEFAULT_CRYPT_KEY);

        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        $data = encode('aes-256-cbc', base64_encode($iv), base64_encode($ciphertext));

        return base64_encode($data);
    }
}

if (! function_exists('decrypt')) {
    function decrypt(string $encrypted): string
    {
        $key = env('CRYPT_KEY', DEFAULT_CRYPT_KEY);

        $set = decode($encrypted);
        if ($set === null) {
            throw new InvalidArgumentException('Invalid encrypted format.');
        }
        if ($set->get('algo') !== 'aes-256-cbc') {
            throw new InvalidArgumentException('Unknown encryption algorithm.');
        }

        $iv = base64_decode($set->get('salt'));
        $ciphertext = base64_decode($set->get('data'));

        return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }
}

if (! function_exists('encode')) {
    function encode(string $algo, string $salt, string $data): string
    {
        return json_encode([
            'algo' => $algo,
            'salt' => $salt,
            'data' => $data,
        ]);
    }
}

if (! function_exists('decode')) {
    function decode(mixed $encrypted): ?Set
    {
        $decoded = json_decode(base64_decode($encrypted), true);
        return (isset($decoded['algo'], $decoded['salt'], $decoded['data']))
            ? new Set($decoded)
            : null;
    }
}
