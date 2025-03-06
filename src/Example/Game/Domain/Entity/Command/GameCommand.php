<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Domain\Entity\Command;

use Serendipity\Domain\Entity\Entity;
use Serendipity\Domain\Support\Reflective\Type\Text;

class GameCommand extends Entity
{
    public function __construct(
        public readonly string $name,
        #[Text(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
        public readonly string $slug,
        public readonly array $data = [],
    ) {
    }
}
