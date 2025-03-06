<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Domain\Entity;

use DateTimeImmutable;
use Serendipity\Domain\Support\Reflective\Behaviour\Managed;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;

class Game extends GameCommand
{
    public function __construct(
        #[Managed(management: 'id')]
        public readonly string $id,
        #[Managed(management: 'now')]
        public readonly DateTimeImmutable $createdAt,
        #[Managed(management: 'now')]
        public readonly DateTimeImmutable $updatedAt,
        string $name,
        string $slug,
        array $data = [],
    ) {
        parent::__construct($name, $slug, $data);
    }
}
