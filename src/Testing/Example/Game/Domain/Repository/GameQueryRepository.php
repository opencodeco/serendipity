<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Domain\Repository;

use Serendipity\Testing\Example\Game\Domain\Collection\GameCollection;
use Serendipity\Testing\Example\Game\Domain\Entity\Game;

interface GameQueryRepository
{
    public function getGame(string $id): ?Game;

    public function getGames(array $filters = []): GameCollection;
}
