<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

use DateTimeInterface;
use stdClass;

class Union
{
    public function __construct(
        public readonly int|string $builtin,
        public readonly null|int|string $nullable,
        public readonly DateTimeInterface|stdClass $native,
    ) {
    }
}
