<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Testing\Example\Game\Infrastructure\Postgres;

use Serendipity\Test\Hyperf\Testing\Example\Game\InfrastructureTestCase;
use Serendipity\Testing\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Testing\Example\Game\Domain\Entity\Game;
use Serendipity\Testing\Example\Game\Infrastructure\Repository\Postgres\PostgresGameCommandRepository;
use Serendipity\Testing\Extension\BuilderExtension;

final class PostgresGameCommandRepositoryTest extends InfrastructureTestCase
{
    use BuilderExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpResource('games', 'postgres');
    }

    public function testShouldPersistSuccessfully(): void
    {
        $repository = $this->make(PostgresGameCommandRepository::class);
        $values = $this->faker()->fake(GameCommand::class);
        $game = $this->builder()->build(GameCommand::class, $values);
        $id = $repository->persist($game);

        $this->assertHas(['id' => $id]);
    }

    public function testShouldDestroySuccessfully(): void
    {
        $values = $this->seed(Game::class);
        $id = $values->get('id');
        $repository = $this->make(PostgresGameCommandRepository::class);
        $repository->destroy($id);
        $this->assertHasNot(['id' => $id]);
    }
}
