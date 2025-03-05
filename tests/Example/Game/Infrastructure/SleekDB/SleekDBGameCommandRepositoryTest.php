<?php

declare(strict_types=1);

namespace Serendipity\Test\Example\Game\Infrastructure\SleekDB;

use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Example\Game\Domain\Entity\Game;
use Serendipity\Example\Game\Infrastructure\Repository\SleekDB\SleekDBGameCommandRepository;
use Serendipity\Test\Example\Game\InfrastructureCase;
use Serendipity\Testing\Extension\BuilderExtension;

/**
 * @internal
 */
class SleekDBGameCommandRepositoryTest extends InfrastructureCase
{
    use BuilderExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpResource('games', 'sleek');
    }

    public function testShouldPersistSuccessfully(): void
    {
        $repository = $this->make(SleekDBGameCommandRepository::class);
        $values = $this->faker()->fake(GameCommand::class);
        $game = $this->builder()->build(GameCommand::class, $values);
        $id = $repository->persist($game);

        $this->assertHas([['id', '=', $id]]);
    }

    public function testShouldDestroySuccessfully(): void
    {
        $repository = $this->make(SleekDBGameCommandRepository::class);

        $values = $this->seed(Game::class);
        $id = $values->get('id');

        $this->assertHasExactly(1, [['id', '=', $id]]);

        $repository->destroy($id);

        $this->assertHasNot([['id', '=', $id]]);
    }
}
