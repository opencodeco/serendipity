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
    protected function entity(Serializer $serializer, array $data, array $fixes = []): mixed
    {
        if (empty($data)) {
            return null;
        }
        $datum = array_shift($data);
        $datum = $this->toArray($datum);
        $datum = array_merge($fixes, $datum);
        return $serializer->serialize($datum);
    }

    /**
     * @template T of Collection
     * @param class-string<T> $collection
     *
     * @return T
     */
    protected function collection(Serializer $serializer, array $data, string $collection, array $fixes = []): mixed {
        $instance = new $collection();
        foreach ($data as $datum) {
            $datum = $this->toArray($datum);
            $datum = array_merge($fixes, $datum);
            $instance->push($serializer->serialize($datum));
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
