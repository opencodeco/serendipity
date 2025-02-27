<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serializing\Serialize;

use DateTime;
use Serendipity\Domain\Entity\Entity;

class BuilderTestStubWithConstructor extends Entity
{
    public function __construct(
        public readonly int $id,
        public readonly float $price,
        public readonly string $name,
        public readonly bool $isActive,
        public readonly BuilderTestStubWithNoConstructor $more,
        public readonly ?DateTime $createdAt,
        public readonly ?BuilderTestStubWithNoParameters $no,
        public readonly array $tags = [],
        public readonly BuilderTestEnum $enum = BuilderTestEnum::ONE,
        ?string $foo = null,
    ) {
    }
}
