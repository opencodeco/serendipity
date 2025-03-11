<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing;

use ReflectionException;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Database\Document\MongoFactory;
use Serendipity\Infrastructure\Repository\Adapter\MongoDeserializerFactory;
use Serendipity\Infrastructure\Repository\Adapter\MongoSerializerFactory;
use Serendipity\Testing\Faker\Faker;
use Serendipity\Testing\Resource\Helper;

use function Serendipity\Type\Cast\arrayify;

final class MongoHelper extends Helper
{
    public function __construct(
        Faker $faker,
        MongoSerializerFactory $serializerFactory,
        MongoDeserializerFactory $deserializerFactory,
        private readonly MongoFactory $factory,
    ) {
        parent::__construct($faker, $serializerFactory, $deserializerFactory);
    }

    public function truncate(string $resource): void
    {
        $collection = $this->factory->make($resource);
        $collection->deleteMany([]);
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @throws ReflectionException
     */
    public function seed(string $type, string $resource, array $override = []): Set
    {
        $data = $this->fake($type, $override);

        $insertOneResult = $this->factory->make($resource)->insertOne($data);
        return new Set(array_merge($data, ['_id' => $insertOneResult]));
    }

    public function count(string $resource, array $filters = []): int
    {
        $database = $this->factory->make($resource);
        $array = arrayify($database->find($filters)->toArray());
        return count($array);
    }
}
