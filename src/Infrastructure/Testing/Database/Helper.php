<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Database;

use ReflectionException;
use Serendipity\Domain\Contract\DeserializerFactory;
use Serendipity\Domain\Contract\SerializerFactory;
use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\Faker\Faker;

use function array_merge;
use function Serendipity\Type\Json\encode;
use function sprintf;

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

    public function assertHas(string $resource, array $filters): void
    {
        $count = $this->count($resource, $filters);
        $message = sprintf(
            "Expected to find at least one item in resource '%s' with filters '%s'",
            $resource,
            $this->json($filters),
        );
        assert($count > 0, $message);
    }

    public function assertHasNot(string $resource, array $filters): void
    {
        $count = $this->count($resource, $filters);
        $message = sprintf(
            "Expected to not find any item in resource '%s' with filters '%s'",
            $resource,
            $this->json($filters)
        );
        assert($count === 0, $message);
    }

    public function assertHasCount(int $expected, string $resource, array $filters): void
    {
        $count = $this->count($resource, $filters);
        $message = sprintf(
            "Expected to find %d items in resource '%s' with filters '%s', but found %d",
            $expected,
            $resource,
            $this->json($filters),
            $count
        );
        assert($count === $expected, $message);
    }

    public function assertIsEmpty(string $resource): void
    {
        $count = $this->count($resource, []);
        $message = sprintf(
            "Expected resource '%s' to be empty, but found %d items",
            $resource,
            $count
        );
        assert($count === 0, $message);
    }

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

    abstract protected function count(string $resource, array $filters): int;

    protected function json(array $filters): ?string
    {
        return encode($filters);
    }
}
