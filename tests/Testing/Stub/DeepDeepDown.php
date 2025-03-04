<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

final readonly class DeepDeepDown
{
    public function __construct(
        public EntityStub $stub,
        public Builtin $builtin,
    ) {
    }
}
