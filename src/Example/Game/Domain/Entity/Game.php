<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Domain\Entity;

use DateTimeImmutable;
use Serendipity\Domain\Support\Meta\Options;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;

class Game extends GameCommand
{
    public function __construct(
        #[Options(['managed' => 'id'])]
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
