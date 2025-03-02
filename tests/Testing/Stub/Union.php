<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

use DateTimeInterface;
use stdClass;

class Union
{
    public function __construct(
        public readonly string|int $builtin,
        public readonly string|int|null $nullable,
        public readonly stdClass|DateTimeInterface $native,
    ) {
    }
}
