<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionClass;

class Target
{
    public function __construct(
        public readonly ReflectionClass $reflection,
        public readonly array $parameters = [],
    ) {
    }
}
