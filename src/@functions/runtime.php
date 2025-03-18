<?php

/**
 * Runtime functions.
 */
declare(strict_types=1);

namespace Serendipity\Runtime;

use Hyperf\Coroutine\Coroutine;

/**
 * @SuppressWarnings(StaticAccess)
 */
if (! function_exists(__NAMESPACE__ . '\coroutine')) {
    function coroutine(callable $callback): int
    {
        return Coroutine::create($callback);
    }
}

if (! function_exists(__NAMESPACE__ . '\invoke')) {
    function invoke(callable $callback, mixed ...$args): mixed
    {
        return $callback(...$args);
    }
}
