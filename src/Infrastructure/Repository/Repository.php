<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository;

use Serendipity\Domain\Collection\Collection;
use Serendipity\Domain\Contract\Adapter\Serializer;

use function Serendipity\Type\Cast\toArray;

class Repository
{
    protected Serializer $serializer;

    /**
     * @template T of Collection
     * @param class-string<T> $collection
     * @param mixed $result
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
