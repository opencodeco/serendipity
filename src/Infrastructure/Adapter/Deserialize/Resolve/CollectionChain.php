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
use function Serendipity\Type\Cast\stringify;

class CollectionChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $candidate = $this->detectCollectionName($parameter);
        $className = stringify($candidate);
        if (! $candidate || ! is_subclass_of($className, Collection::class) || ! $value instanceof Collection) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveCollection($parameter, $className, $value);
    }

    /**
     * @param Collection|class-string<Collection> $className
     * @throws ReflectionException
     */
    private function resolveCollection(
        ReflectionParameter $parameter,
        Collection|string $className,
        Collection $value
    ): Value {
        $reflection = new ReflectionClass($className);
        if (! $reflection->isInstance($value)) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveCollectionValue($value);
    }

    private function resolveCollectionValue(Collection $value): Value
    {
        return new Value($value->map(fn (object $instance): array => (array) $this->demolish($instance)));
    }
}
