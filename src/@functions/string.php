<?php

declare(strict_types=1);

namespace Serendipity\Type\String;

use Jawira\CaseConverter\Convert;

use function Serendipity\Type\Cast\stringify;

if (! function_exists(__NAMESPACE__ . '\snakify')) {
    function snakify(string $string, bool $includeDigits = true): string
    {
        $string = (new Convert($string))->toSnake();
        if ($includeDigits) {
            return ltrim(stringify(preg_replace('/(\d+)/', '_$0', $string)), '_');
        }
        return $string;
    }
}
