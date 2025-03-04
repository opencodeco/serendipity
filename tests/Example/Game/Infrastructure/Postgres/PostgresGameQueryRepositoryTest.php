<?php

declare(strict_types=1);

namespace Serendipity\Test\Example\Game\Infrastructure\Postgres;

use Serendipity\Example\Game\Domain\Entity\Game;
use Serendipity\Example\Game\Infrastructure\Repository\Postgres\PostgresGameQueryRepository;
use Serendipity\Test\Example\Game\InfrastructureTestCase;
use Serendipity\Testing\Extension\InstrumentalExtension;

use function Hyperf\Collection\collect;

/**
 * @internal
 */
class PostgresGameQueryRepositoryTest extends InfrastructureTestCase
{
    use InstrumentalExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpResource('games', 'postgres');
    }

    public function testShouldReadGameSuccessfully(): void
    {
        $values = $this->seed(Game::class);

        $repository = $this->make(PostgresGameQueryRepository::class);
        $game = $repository->getGame($values->get('id'));
        $this->assertEquals($values->get('name'), $game->name);
    }

    final public function testShouldReturnNullWhenGameNotExists(): void
    {
        $id = $this->instrumental()->id();
        $repository = $this->make(PostgresGameQueryRepository::class);
        $this->assertNull($repository->getGame($id));
    }

    public function testGetGamesReturnsGameCollection(): void
    {
        $this->seed(Game::class);
        $this->seed(Game::class);

        $repository = $this->make(PostgresGameQueryRepository::class);
        $games = $repository->getGames();

        $this->assertCount(2, $games);
    }

    public function testGetGamesContainsExpectedGames(): void
    {
        $values = $this->seed(Game::class);
        $this->seed(Game::class);

        $repository = $this->make(PostgresGameQueryRepository::class);
        $all = $repository->getGames()->all();
        $count = collect($all)
            ->filter(fn ($game) => $game->id === $values->get('id'))
            ->count();
        $this->assertEquals(1, $count);
    }

    public function testGetGamesReturnsEmptyCollectionWhenNoGames(): void
    {
        $repository = $this->make(PostgresGameQueryRepository::class);
        $games = $repository->getGames();
        $this->assertCount(0, $games);
    }
}
