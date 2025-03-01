<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Domain\Entity;

use DateTimeImmutable;
use Serendipity\Testing\Example\Game\Domain\Entity\Command\GameCommand;

class Game extends GameCommand
{
    public function __construct(
        public readonly string $id,
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt,
        string $name,
        string $slug,
        array $data = [],
    ) {
        parent::__construct($name, $slug, $data);
    }
}
