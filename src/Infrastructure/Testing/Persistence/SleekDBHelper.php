<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Persistence;

use ReflectionException;
use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\Adapter\Serializing\DeserializerFactory;
use Serendipity\Infrastructure\Adapter\Serializing\SerializerFactory;
use Serendipity\Infrastructure\Persistence\Factory\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Testing\Faker\Faker;
use SleekDB\Exceptions\IdNotAllowedException;
use SleekDB\Exceptions\InvalidArgumentException;
use SleekDB\Exceptions\InvalidConfigurationException;
use SleekDB\Exceptions\IOException;
use SleekDB\Exceptions\JsonException as SleekDBJsonExceptionAlias;

final class SleekDBHelper extends Helper
{
    public function __construct(
        Faker $faker,
        SerializerFactory $serializerFactory,
        DeserializerFactory $deserializerFactory,
        private readonly SleekDBDatabaseFactory $factory,
    ) {
        parent::__construct($faker, $serializerFactory, $deserializerFactory);
    }

    /**
     * @throws IOException
     * @throws InvalidConfigurationException
     * @throws InvalidArgumentException
     */
    public function truncate(string $resource): void
    {
        $database = $this->factory->make($resource);
        $database->deleteBy(['_id', '>=', 0]);
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @throws ReflectionException
     * @throws IOException
     * @throws IdNotAllowedException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws SleekDBJsonExceptionAlias
     */
    public function seed(string $type, string $resource, array $override = []): Values
    {
        $data = $this->fake($type, $override);

        $generatedId = $this->factory->make($resource)->insert($data);
        return new Values(array_merge($data, ['_id' => $generatedId]));
    }

    public function count(string $resource, array $filters = []): int
    {
        $database = $this->factory->make($resource);
        return count($database->findBy($filters));
    }
}
