<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Domain\Repository;

use Serendipity\Domain\Exception\ManagedException;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;

interface GameCommandRepository
{
    /**
     * @throws ManagedException
     */
    public function persist(GameCommand $game): string;

    public function destroy(string $id): bool;
}
