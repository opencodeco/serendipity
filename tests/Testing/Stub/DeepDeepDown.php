<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

use Serendipity\Test\Testing\Stub\Type\EmptyEnum;

final readonly class DeepDeepDown
{
    public function __construct(
        public EntityStub $stub,
        public Builtin $builtin,
        public EmptyEnum $empty,
    ) {
    }
}
