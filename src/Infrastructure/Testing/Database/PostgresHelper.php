<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Database;

use Hyperf\DB\DB as Database;
use ReflectionException;
use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\Faker\Faker;
use Serendipity\Infrastructure\Persistence\Factory\HyperfDBFactory;
use Serendipity\Infrastructure\Persistence\Serializing\RelationalDeserializerFactory;
use Serendipity\Infrastructure\Persistence\Serializing\RelationalSerializerFactory;

use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function count;
use function implode;
use function Serendipity\Type\Util\extractNumeric;
use function Serendipity\Type\Cast\toArray;
use function sprintf;
use function str_repeat;

final class PostgresHelper extends Helper
{
    private readonly Database $database;

    public function __construct(
        Faker $faker,
        RelationalSerializerFactory $serializerFactory,
        RelationalDeserializerFactory $deserializerFactory,
        HyperfDBFactory $hyperfDBFactory,
    ) {
        parent::__construct($faker, $serializerFactory, $deserializerFactory);

        $this->database = $hyperfDBFactory->make('postgres');
    }

    public function truncate(string $resource): void
    {
        $this->database->execute(sprintf('delete from %s where true', $resource));
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @throws ReflectionException
     */
    public function seed(string $type, string $resource, array $override = []): Values
    {
        $data = $this->fake($type, $override);
        $fields = array_keys($data);
        $fields = array_map(static fn (string $field) => sprintf('"%s"', $field), $fields);
        $columns = implode(',', $fields);
        $values = str_repeat('?,', count($fields) - 1) . '?';

        $query = sprintf('insert into "%s" (%s) values (%s)', $resource, $columns, $values);
        $bindings = array_values($data);

        $this->database->execute($query, $bindings);
        return new Values($data);
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
        $query = sprintf(
            'select count(*) as count from %s where %s',
            sprintf('"%s"', $resource),
            $where
        );
        $bindings = array_values(array_filter($filters, static fn (mixed $value) => $value !== null));
        $result = toArray($this->database->fetch($query, $bindings));
        return (int) extractNumeric($result, 'count', 0);
    }
}
