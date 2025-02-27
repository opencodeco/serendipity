<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Persistence;

use Hyperf\DB\DB as Database;
use Hyperf\DB\Exception\QueryException;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Domain\Exception\UniqueKeyViolationException;
use Serendipity\Infrastructure\Persistence\Factory\HyperfDBFactory;
use Serendipity\Infrastructure\Persistence\Serializing\RelationalDeserializerFactory;
use Serendipity\Infrastructure\Persistence\Serializing\RelationalSerializerFactory;

use Throwable;

use function Serendipity\Type\Cast\toString;

abstract class PostgresRepository
{
    protected readonly Database $database;

    public function __construct(
        protected readonly Generator $generator,
        protected readonly RelationalDeserializerFactory $deserializerFactory,
        protected readonly RelationalSerializerFactory $serializerFactory,
        HyperfDBFactory $hyperfDBFactory,
    ) {
        $this->database = $hyperfDBFactory->make('postgres');
    }

    /**
     * @param object $instance
     * @param array<string> $fields
     * @param array<string,mixed> $default
     * @param array<string,string> $generate
     * @return array
     * @throws GeneratingException
     */
    protected function bindings(
        object $instance,
        array $fields,
        array $default = [],
        array $generate = ['created_at' => 'now', 'updated_at' => 'now']
    ): array {
        $values = $this->deserializerFactory->make($instance::class)
            ->deserialize($instance);

        $values = array_merge($default, $values);

        foreach ($generate as $field => $type) {
            $value = match ($type) {
                'now' => $this->generator->now(),
                'id' => $this->generator->id(),
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
     * @return string
     */
    protected function columns(array $fields): string
    {
        return implode(', ', array_map(fn ($field) => sprintf('"%s"', $field), $fields));
    }

    /**
     * @param array<string> $fields
     * @return string
     */
    protected function values(array $fields): string
    {
        return implode(', ', array_fill(0, count($fields), '?'));
    }

    protected function detectUniqueKeyViolation(QueryException|Throwable $exception): ?UniqueKeyViolationException
    {
        $message = $exception->getMessage();
        $pattern = '/duplicate key value violates unique constraint "([^"]+)"\s+DETAIL:\s+Key \(([^)]+)\)=\(([^)]+)\) already exists\./m';
        if (! preg_match($pattern, $message, $matches)) {
            return null;
        }
        $resource = toString($matches[1] ?? null);
        $key = toString($matches[2] ?? null);
        $value = toString($matches[3] ?? null);
        return new UniqueKeyViolationException($key, $value, $resource, $exception);
    }
}
