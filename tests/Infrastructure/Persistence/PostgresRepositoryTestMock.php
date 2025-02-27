<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Persistence;

use Serendipity\Infrastructure\Persistence\PostgresRepository;

class PostgresRepositoryTestMock extends PostgresRepository
{
    public function expose(
        object $instance,
        array $fields,
        array $default = [],
        array $generate = ['created_at' => 'now', 'updated_at' => 'now']
    ): array {
        return parent::bindings($instance, $fields, $default, $generate);
    }
}
