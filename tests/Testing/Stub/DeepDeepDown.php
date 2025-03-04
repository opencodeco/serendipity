<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

final class DeepDeepDown
{
    public function __construct(
        public readonly EntityStub $stub,
        public readonly Builtin $builtin,
    ) {
    }
}
