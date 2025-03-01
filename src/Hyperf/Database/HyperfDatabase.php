<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Database;

use Closure;
use Hyperf\DB\DB as Database;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Database\Relational\RelationalDatabase;

use function Serendipity\Type\Cast\toArray;

class HyperfDatabase implements RelationalDatabase
{
    public function __construct(
        public readonly string $connection,
        private readonly Database $database
    ) {
    }

    /**
     * Open transaction (Support transaction nesting)
     * @return void
     */
    public function beginTransaction(): void
    {
        $this->database->beginTransaction();
    }

    /**
     * Commit transaction (Support transaction nesting)
     * @return void
     */
    public function commit(): void
    {
        $this->database->commit();
    }

    /**
     * Rollback transaction (Support transaction nesting)
     * @return void
     */
    public function rollback(): void
    {
        $this->database->rollback();
    }

    /**
     * Insert data, return the primary key ID, non-auto-incrementing primary key returns 0
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function insert(string $query, array $bindings = []): int
    {
        return $this->database->insert($query, $bindings);
    }

    /**
     * Execute SQL to return the number of rows affected
     * @param string $query
     * @param array $bindings
     * @return mixed
     */
    public function execute(string $query, array $bindings = []): int
    {
        return $this->database->execute($query, $bindings);
    }

    /**
     * Query SQL, return a list of result sets
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public function query(string $query, array $bindings = []): array
    {
        return toArray($this->database->query($query, $bindings));
    }

    /**
     * Query SQL to return the first row of the result set
     * @param string $query
     * @param array $bindings
     * @return Set
     */
    public function fetch(string $query, array $bindings = []): Set
    {
        return Set::createFrom($this->database->fetch($query, $bindings) ?? []);
    }

    /**
     * Specify the database to connect to
     * @param Closure $closure
     * @return void
     */
    public function run(Closure $closure): void
    {
        $this->database->run($closure);
    }
}
