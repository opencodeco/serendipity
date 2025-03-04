<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

final class Deep
{
    public function __construct(
        public readonly string $what,
        public readonly DeepDown $deepDown
    ) {
    }
}
