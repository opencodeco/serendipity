<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Infrastructure\Repository\Postgres;

use Serendipity\Infrastructure\Repository\PostgresRepository;
use Serendipity\Testing\Example\Game\Domain\Collection\GameCollection;
use Serendipity\Testing\Example\Game\Domain\Entity\Game;
use Serendipity\Testing\Example\Game\Domain\Repository\GameQueryRepository;

class PostgresGameQueryRepository extends PostgresRepository implements GameQueryRepository
{
    public function getGame(string $id): ?Game
    {
        /* @noinspection SqlNoDataSourceInspection, SqlResolve */
        $query = 'select "id", "created_at", "updated_at", "name", "slug", "data" from "games" where "id" = ?';
        $bindings = [$id];
        $data = $this->database->query($query, $bindings);
        $serializer = $this->serializerFactory->make(Game::class);
        return $this->entity($serializer, $data);
    }

    public function getGames(array $filters = []): GameCollection
    {
        /* @noinspection SqlNoDataSourceInspection, SqlResolve */
        $query = 'select "id", "created_at", "updated_at", "name", "slug", "data" from "games"';
        $data = $this->database->query($query);
        $serializer = $this->serializerFactory->make(Game::class);
        return $this->collection($serializer, $data, GameCollection::class);
    }
}
