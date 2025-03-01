<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository;

use Serendipity\Domain\Contract\Adapter\Serializer;

use function Serendipity\Type\Cast\toArray;

class Repository
{
    protected readonly Serializer $serializer;

    /**
     * @template T of mixed
     * @param class-string<T> $collection
     * @param array<string, mixed> $result
     *
     * @return T
     */
    protected function hydrate(string $collection, mixed $result): mixed
    {
        $instance = new $collection();
        /** @var array<array<string, mixed>> $data */
        $data = toArray($result);
        foreach ($data as $datum) {
            $instance->push($this->serializer->serialize($datum));
        }
        return $instance;
    }
}
