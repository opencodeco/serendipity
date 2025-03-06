<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository;

use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Infrastructure\Database\Managed;
use Serendipity\Infrastructure\Database\Relational\RelationalDatabase;
use Serendipity\Infrastructure\Database\Relational\RelationalDatabaseFactory;
use Serendipity\Infrastructure\Repository\Adapter\RelationalDeserializerFactory;
use Serendipity\Infrastructure\Repository\Adapter\RelationalSerializerFactory;

abstract class PostgresRepository extends Repository
{
    protected readonly RelationalDatabase $database;

    public function __construct(
        protected readonly Managed $managed,
        protected readonly RelationalDeserializerFactory $deserializerFactory,
        protected readonly RelationalSerializerFactory $serializerFactory,
        RelationalDatabaseFactory $relationalDatabaseFactory,
    ) {
        $this->database = $relationalDatabaseFactory->make('postgres');
    }

    /**
     * @param array<string> $fields
     * @param array<string,mixed> $default
     * @param array<string,string> $managed
     * @throws GeneratingException
     */
    protected function bindings(
        object $instance,
        array $fields,
        array $default = [],
        array $managed = ['created_at' => 'now', 'updated_at' => 'now']
    ): array {
        $values = $this->deserializerFactory->make($instance::class)
            ->deserialize($instance);

        $values = array_merge($default, $values);

        foreach ($managed as $field => $type) {
            $value = match ($type) {
                'now' => $this->managed->now(),
                'id' => $this->managed->id(),
                default => throw new GeneratingException($type)
            };
            $values[$field] = $value;
        }

        return array_map(
            static fn (string $field) => $values[$field] ?? null,
            $fields
        );
    }

    /**
     * @param array<string> $fields
     */
    protected function columns(array $fields): string
    {
        return implode(', ', array_map(fn ($field) => sprintf('"%s"', $field), $fields));
    }

    /**
     * @param array<string> $fields
     */
    protected function wildcards(array $fields): string
    {
        return implode(', ', array_fill(0, count($fields), '?'));
    }
}
