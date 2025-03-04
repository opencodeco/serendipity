<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

final readonly class Deep
{
    public function __construct(
        public string $what,
        public DeepDown $deepDown
    ) {
    }
}
