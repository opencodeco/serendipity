<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing\Extension;

use function Hyperf\Support\make;

trait MakeExtension
{
    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     */
    protected function make(string $class, array $args = []): mixed
    {
        return make($class, $args);
    }
}
