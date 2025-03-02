<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

final class NullableAndOptional
{
    public function __construct(
        public readonly ?string $nullable,
        public readonly string|int|null $union,
        public readonly int $optional = 10,
    ) {
    }
}
