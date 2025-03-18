<?php

declare(strict_types=1);

namespace Serendipity\Runtime;

use Hyperf\Coroutine\Coroutine;

if (! function_exists(__NAMESPACE__ . '\coroutine')) {
    /**
     * @SuppressWarnings(StaticAccess)
     */
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
