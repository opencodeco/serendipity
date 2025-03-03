<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

class Complex
{
    public function __construct(
        public readonly EntityStub $entity,
        public readonly Native $native,
        public readonly Builtin $builtin,
    ) {
    }
}
