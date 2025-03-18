<?php

declare(strict_types=1);

namespace Serendipity\Type\String;

use function Serendipity\Type\Cast\stringify;

if (! function_exists(__NAMESPACE__ . '\snakify')) {
    function snakify(string $string): string
    {
        $string = stringify(preg_replace('/[A-Z_0-9]/', '_$0', $string));
        return strtolower(ltrim($string, '_'));
    }
}
