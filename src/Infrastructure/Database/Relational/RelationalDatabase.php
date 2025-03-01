<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Database\Relational;

use Closure;

interface RelationalDatabase
{
    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;

    public function insert(string $query, array $bindings = []): int;

    public function execute(string $query, array $bindings = []): int;

    public function query(string $query, array $bindings = []): array;

    public function fetch(string $query, array $bindings = []): mixed;

    public function run(Closure $closure): void;
}
