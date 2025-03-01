<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Database;

use Closure;
use Hyperf\DB\DB as Database;
use Serendipity\Infrastructure\Database\Relational\RelationalDatabase;

class HyperfDatabase implements RelationalDatabase
{
    public function __construct(
        public readonly string $connection,
        private readonly Database $database
    ) {
    }

    public function beginTransaction(): mixed
    {
        return $this->database->beginTransaction();
    }

    public function commit(): mixed
    {
        return $this->database->commit();
    }

    public function rollback(): mixed
    {
        return $this->database->rollback();
    }

    public function insert(string $query, array $bindings = []): mixed
    {
        return $this->database->insert($query, $bindings);
    }

    public function execute(string $query, array $bindings = []): mixed
    {
        return $this->database->execute($query, $bindings);
    }

    public function query(string $query, array $bindings = []): mixed
    {
        return $this->database->query($query, $bindings);
    }

    public function fetch(string $query, array $bindings = []): mixed
    {
        return $this->database->fetch($query, $bindings);
    }

    public function run(Closure $closure): mixed
    {
        return $this->database->run($closure);
    }
}
