<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

final readonly class DeepDown
{
    public function __construct(
        public DeepDeepDown $deepDeepDown,
        public Builtin $builtin,
    ) {
    }
}
