<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Collection\Collection;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use function Serendipity\Type\Cast\arrayify;

class CollectionValue extends Resolver
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $collectionName = $this->detectCollectionName($parameter);
        if ($collectionName) {
            $field = $this->casedField($parameter);
            $value = $set->get($field);
            return $this->resolveCollection($collectionName, $value);
        }
        return parent::resolve($parameter, $set);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveCollection(string $collectionName, mixed $value): Value
    {
        $reflection = new ReflectionClass($collectionName);
        $type = $this->detectCollectionType($reflection);

        /** @var Collection $collection */
        $collection = $reflection->newInstance();
        if ($type === null || ! is_array($value)) {
            return new Value($collection);
        }
        foreach ($value as $datum) {
            $datum = arrayify($datum);
            $set = Set::createFrom($datum);
            $instance = $this->build($type, $set);
            $collection->push($instance);
        }
        return new Value($collection);
    }
}
