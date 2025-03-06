<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

use DateTimeImmutable;
use Serendipity\Domain\Support\Reflective\Attributes\Managed;

class EntityManaged
{
    public function __construct(
        #[Managed(management: 'id')]
        public readonly string $id,
        #[Managed(management: 'now')]
        public readonly DateTimeImmutable $createdAt,
        #[Managed(management: 'now')]
        public readonly DateTimeImmutable $updatedAt,
        public readonly string $name,
    ) {
    }
}
