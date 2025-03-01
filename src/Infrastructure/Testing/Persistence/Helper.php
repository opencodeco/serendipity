<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Persistence;

use ReflectionException;
use Serendipity\Domain\Contract\Adapter\DeserializerFactory;
use Serendipity\Domain\Contract\Adapter\SerializerFactory;
use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\Testing\Faker\Faker;

use function array_merge;

abstract class Helper
{
    public function __construct(
        private readonly Faker $faker,
        private readonly SerializerFactory $serializerFactory,
        private readonly DeserializerFactory $deserializerFactory,
    ) {
    }

    abstract public function truncate(string $resource): void;

    abstract public function seed(string $type, string $resource, array $override = []): Values;

    abstract public function count(string $resource, array $filters): int;

    /**
     * @template T of object
     * @param class-string<T> $type
     * @throws ReflectionException
     */
    final protected function fake(string $type, array $override): array
    {
        $fake = $this->faker->fake($type);
        $instance = $this->serializerFactory->make($type)->serialize($fake->toArray());
        $datum = $this->deserializerFactory->make($type)->deserialize($instance);
        return array_merge($datum, $override);
    }
}
