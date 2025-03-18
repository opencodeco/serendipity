<?php

declare(strict_types=1);

namespace Serendipity\Crypt;

use InvalidArgumentException;
use RuntimeException;
use Serendipity\Domain\Support\Set;

use function Serendipity\Type\Cast\arrayify;
use function Serendipity\Type\Cast\stringify;
use function Serendipity\Type\Json\decode;
use function Serendipity\Type\Json\encode;
use function Serendipity\Type\Util\extractString;

if (! defined('DEFAULT_CRYPT_KEY')) {
    define('DEFAULT_CRYPT_KEY', base64_encode(stringify(random_bytes(32))));
}

if (! function_exists('encrypt')) {
    function encrypt(string $plaintext, string $key = DEFAULT_CRYPT_KEY): string
    {
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) {
            throw new RuntimeException(sprintf("Encryption failed: '%s'.", $plaintext));
        }

        $data = group('aes-256-cbc', base64_encode($iv), base64_encode($ciphertext));
        if ($data === null) {
            throw new RuntimeException(sprintf("Encryption failed: '%s'.", $plaintext));
        }
        return base64_encode($data);
    }
}

if (! function_exists('decrypt')) {
    function decrypt(string $encrypted, string $key = DEFAULT_CRYPT_KEY): string
    {
        $set = ungroup($encrypted);
        if ($set === null) {
            throw new InvalidArgumentException('Invalid encrypted format.');
        }
        if ($set->get('algo') !== 'aes-256-cbc') {
            throw new InvalidArgumentException('Unknown encryption algorithm.');
        }

        $iv = base64_decode(stringify($set->get('salt')));
        $ciphertext = base64_decode(stringify($set->get('data')));

        $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) {
            throw new RuntimeException(sprintf("Decryption failed: '%s'.", $encrypted));
        }
        return $decrypted;
    }
}

if (! function_exists('group')) {
    function group(string $algo, string $salt, string $data): ?string
    {
        return encode([
            'algo' => $algo,
            'salt' => $salt,
            'data' => $data,
        ]);
    }
}

if (! function_exists('ungroup')) {
    function ungroup(string $encrypted): ?Set
    {
        $decoded = arrayify(decode(base64_decode($encrypted)));
        $algo = extractString($decoded, 'algo');
        $salt = extractString($decoded, 'salt');
        $data = extractString($decoded, 'data');
        return ($algo && $salt && $data)
            ? new Set([
                'algo' => $algo,
                'salt' => $salt,
                'data' => $data,
            ])
            : null;
    }
}
