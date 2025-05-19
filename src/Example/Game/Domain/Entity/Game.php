<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Domain\Entity;

use Serendipity\Domain\Support\Reflective\Attribute\Managed;
use Serendipity\Domain\Support\Reflective\Attribute\Pattern;
use Serendipity\Domain\Type\Timestamp;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;

class Game extends GameCommand
{
    public function __construct(
        #[Managed('id')]
        public readonly string $id,
        #[Managed('timestamp')]
        public readonly Timestamp $createdAt,
        #[Managed('timestamp')]
        public readonly Timestamp $updatedAt,
        #[Pattern('/^[a-zA-Z]{1,255}$/')]
        string $name,
        string $slug,
        array $data = [],
    ) {
        parent::__construct($name, $slug, $data);
    }
}
