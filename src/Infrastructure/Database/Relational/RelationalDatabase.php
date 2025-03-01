<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Database\Relational;

use Closure;

interface RelationalDatabase
{
    public function beginTransaction(): mixed;

    public function commit(): mixed;

    public function rollback(): mixed;

    public function insert(string $query, array $bindings = []): mixed;

    public function execute(string $query, array $bindings = []): mixed;

    public function query(string $query, array $bindings = []): mixed;

    public function fetch(string $query, array $bindings = []): mixed;

    public function run(Closure $closure): mixed;
}
