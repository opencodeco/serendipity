<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing;

use ReflectionException;
use Serendipity\Domain\Support\Set;
use Serendipity\Hyperf\Database\HyperfDatabaseFactory;
use Serendipity\Infrastructure\Database\Relational\RelationalDatabase;
use Serendipity\Infrastructure\Repository\Adapter\RelationalDeserializerFactory;
use Serendipity\Infrastructure\Repository\Adapter\RelationalSerializerFactory;
use Serendipity\Testing\Faker\Faker;
use Serendipity\Testing\Resource\Helper;

use function array_filter;
use function array_keys;
use function array_map;
use function array_shift;
use function array_values;
use function count;
use function implode;
use function Serendipity\Type\Cast\toArray;
use function sprintf;
use function str_repeat;

final class PostgresHelper extends Helper
{
    private readonly RelationalDatabase $database;

    public function __construct(
        Faker $faker,
        RelationalSerializerFactory $serializerFactory,
        RelationalDeserializerFactory $deserializerFactory,
        HyperfDatabaseFactory $hyperfDatabaseFactory,
    ) {
        parent::__construct($faker, $serializerFactory, $deserializerFactory);

        $this->database = $hyperfDatabaseFactory->make('postgres');
    }

    public function truncate(string $resource): void
    {
        /* @noinspection SqlNoDataSourceInspection */
        $this->database->execute(sprintf('delete from %s where true', $resource));
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @throws ReflectionException
     */
    public function seed(string $type, string $resource, array $override = []): Set
    {
        $data = $this->fake($type, $override);
        $fields = array_keys($data);
        $fields = array_map(static fn (string $field) => sprintf('"%s"', $field), $fields);
        $columns = implode(',', $fields);
        $values = str_repeat('?,', count($fields) - 1) . '?';

        /** @noinspection SqlNoDataSourceInspection, SqlResolve */
        $query = sprintf('insert into "%s" (%s) values (%s)', $resource, $columns, $values);
        $bindings = array_values($data);

        $this->database->execute($query, $bindings);
        return new Set($data);
    }

    public function count(string $resource, array $filters): int
    {
        $callback = static function (string $key, mixed $value) {
            if ($value === null) {
                return sprintf('"%s" is null', $key);
            }
            return sprintf('"%s" = ?', $key);
        };
        $wildcards = array_map($callback, array_keys($filters), array_values($filters));
        $where = implode(' and ', $wildcards);
        /** @noinspection SqlNoDataSourceInspection */
        $query = sprintf(
            'select count(*) as count from %s where %s',
            sprintf('"%s"', $resource),
            $where
        );
        $bindings = array_values(array_filter($filters, static fn (mixed $value) => $value !== null));
        $result = $this->database->query($query, $bindings);
        if (empty($result)) {
            return 0;
        }
        $data = toArray(array_shift($result));
        return (int) ($data['count'] ?? 0);
    }
}
