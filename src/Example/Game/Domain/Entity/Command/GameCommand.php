<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Domain\Entity\Command;

use Serendipity\Domain\Entity\Entity;
use Serendipity\Domain\Type\Timestamp;

class GameCommand extends Entity
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly Timestamp $done,
        public readonly array $data = [],
    ) {
    }
}
