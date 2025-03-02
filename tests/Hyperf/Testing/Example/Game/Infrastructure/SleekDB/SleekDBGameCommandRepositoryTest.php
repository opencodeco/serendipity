<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Testing\Example\Game\Infrastructure\SleekDB;

use Serendipity\Test\Hyperf\Testing\Example\Game\InfrastructureTestCase;
use Serendipity\Testing\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Testing\Example\Game\Domain\Entity\Game;
use Serendipity\Testing\Example\Game\Infrastructure\Repository\SleekDB\SleekDBGameCommandRepository;
use Serendipity\Testing\Extension\BuilderExtension;

/**
 * @internal
 */
class SleekDBGameCommandRepositoryTest extends InfrastructureTestCase
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
