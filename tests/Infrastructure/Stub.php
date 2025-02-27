<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure;

class Stub
{
    public function __construct(
        public readonly string $foo,
        public readonly int $bar,
        public readonly Enumeration $baz = Enumeration::BAZ,
    ) {
    }
}
