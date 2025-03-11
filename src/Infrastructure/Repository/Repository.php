<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository;

use Serendipity\Domain\Collection\Collection;
use Serendipity\Domain\Contract\Adapter\Serializer;
use Serendipity\Domain\Entity\Entity;

use function Serendipity\Type\Cast\arrayify;

abstract class Repository
{
    /**
     * @template T of Entity
     * @param Serializer<T> $serializer
     *
     * @return null|T
     */
    protected function entity(Serializer $serializer, array $data): mixed
    {
        if (empty($data)) {
            return null;
        }
        $datum = array_shift($data);
        return $serializer->serialize($this->toArray($datum));
    }

    /**
     * @template T of Collection
     * @param class-string<T> $collection
     *
     * @return T
     */
    protected function collection(Serializer $serializer, array $data, string $collection): mixed
    {
        $instance = new $collection();
        foreach ($data as $datum) {
            $instance->push($serializer->serialize($this->toArray($datum)));
        }
        return $instance;
    }

    /**
     * @return array<string, mixed>
     */
    protected function toArray(mixed $datum): array
    {
        return arrayify($datum);
    }
}
