<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Domain\Entity;

use DateTimeImmutable;
use Serendipity\Domain\Support\Reflective\Attribute\Managed;
use Serendipity\Domain\Support\Reflective\Attribute\Pattern;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;

class Game extends GameCommand
{
    public function __construct(
        #[Managed('id')]
        public readonly string $id,
        #[Managed('timestamp')]
        public readonly DateTimeImmutable $createdAt,
        #[Managed('timestamp')]
        public readonly DateTimeImmutable $updatedAt,
        #[Pattern('/^[a-zA-Z]{1,255}$/')]
        string $name,
        string $slug,
        array $data = [],
    ) {
        parent::__construct($name, $slug, $data);
    }
}
