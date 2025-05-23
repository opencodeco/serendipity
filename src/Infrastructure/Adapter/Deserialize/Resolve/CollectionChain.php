<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize\Resolve;

use DateTimeInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Collection\Collection;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Type\Timestamp;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;

use function Serendipity\Type\Cast\arrayify;

class CollectionChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $candidate = $this->detectCollectionName($parameter);
        if (! $candidate) {
            return parent::resolve($parameter, $value);
        }
        $reflection = new ReflectionClass($candidate);
        if (! $reflection->isInstance($value)) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveCollection($value);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveCollection(Collection $value): Value
    {
        $callback = function (mixed $instance): array {
            return arrayify($this->demolish($instance));
        };
        return new Value(array_map($callback, $value->all()));
    }
}
