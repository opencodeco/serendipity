<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository;

use Hyperf\DB\Exception\QueryException;
use Serendipity\Domain\Exception\UniqueKeyViolationException;
use Serendipity\Infrastructure\Repository\PostgresRepository;
use Throwable;

class PostgresRepositoryTestMock extends PostgresRepository
{
    public function exposeBindings(
        object $instance,
        array $fields,
        array $default = [],
        array $generate = ['created_at' => 'now', 'updated_at' => 'now']
    ): array {
        return $this->bindings($instance, $fields, $default, $generate);
    }

    public function exposeColumns(array $fields): string
    {
        return $this->columns($fields);
    }

    public function exposeWildcards(array $fields): string
    {
        return $this->wildcards($fields);
    }

    public function exposeDetectUniqueKeyViolation(QueryException|Throwable $exception): ?UniqueKeyViolationException
    {
        return $this->detectUniqueKeyViolation($exception);
    }
}
