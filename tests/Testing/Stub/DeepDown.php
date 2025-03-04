<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

final class DeepDown
{
    public function __construct(
        public readonly DeepDeepDown $deepDeepDown,
        public readonly Builtin $builtin,
    ) {
    }
}
