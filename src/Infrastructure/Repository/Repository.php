<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository;

use Serendipity\Domain\Collection\Collection;
use Serendipity\Domain\Contract\Adapter\Serializer;
use Serendipity\Domain\Entity\Entity;

use function Serendipity\Type\Cast\toArray;

abstract class Repository
{
    /**
     * @template T of Entity
     * @param Serializer<T> $serializer
     * @param array<array<string,mixed>> $data
     *
     * @return null|T
     */
    protected function entity(Serializer $serializer, array $data): mixed
    {
        if (empty($data)) {
            return null;
        }
        /* @phpstan-ignore argument.type */
        $datum = toArray(array_shift($data));
        return $serializer->serialize($datum);
    }

    /**
     * @template T of Collection
     * @param class-string<T> $collection
     * @param array<array<string,mixed>> $data
     *
     * @return T
     */
    protected function collection(Serializer $serializer, array $data, string $collection): mixed
    {
        $instance = new $collection();
        foreach ($data as $datum) {
            /* @phpstan-ignore argument.type */
            $instance->push($serializer->serialize($datum));
        }
        return $instance;
    }
}
