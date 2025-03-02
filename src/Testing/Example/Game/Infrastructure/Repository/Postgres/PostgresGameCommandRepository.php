<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Infrastructure\Repository\Postgres;

use Exception;
use Hyperf\DB\Exception\QueryException;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Domain\Exception\UniqueKeyViolationException;
use Serendipity\Hyperf\Database\Support\HasPostgresUniqueConstraint;
use Serendipity\Infrastructure\Repository\PostgresRepository;
use Serendipity\Testing\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Testing\Example\Game\Domain\Repository\GameCommandRepository;

class PostgresGameCommandRepository extends PostgresRepository implements GameCommandRepository
{
    use HasPostgresUniqueConstraint;

    /**
     * @throws GeneratingException
     * @throws UniqueKeyViolationException|Exception
     */
    public function persist(GameCommand $game): string
    {
        $id = $this->instrumental->id();
        $fields = [
            'id',
            'created_at',
            'updated_at',
            'name',
            'slug',
            'data',
        ];
        /* @noinspection SqlNoDataSourceInspection, SqlResolve */
        $query = 'insert into "games" ("id", "created_at", "updated_at", "name", "slug", "data") '
                 . 'values (?, ?, ?, ?, ?, ?)';


        $bindings = $this->bindings($game, $fields, ['id' => $id]);
        try {
            $this->database->execute($query, $bindings);
        } catch (QueryException $exception) {
            $detected = $this->detectUniqueKeyViolation($exception);
            throw $detected ?? $exception;
        }
        return $id;
    }

    public function destroy(string $id): bool
    {
        /* @noinspection SqlNoDataSourceInspection, SqlResolve */
        $query = 'delete from "games" where "id" = ?';
        $affected = $this->database->execute($query, [$id]);
        return $affected > 0;
    }
}
