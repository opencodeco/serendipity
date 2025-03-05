<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Serendipity\Domain\Exception\MetaprogrammingException;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

use function Hyperf\Support\make;

abstract class Resolver extends Faker
{
    protected ?Resolver $previous = null;

    final public function then(Resolver $chain): Resolver
    {
        $chain->previous($this);
        return $chain;
    }

    public function resolve(ReflectionParameter $parameter, ?Set $preset = null): ?Value
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $preset);
        }
        return null;
    }

    final protected function previous(Resolver $previous): void
    {
        $this->previous = $previous;
    }

    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     */
    protected function make(string $class, array $args = []): mixed
    {
        return make($class, $args);
    }

    protected function extractType(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }
        if ($type instanceof ReflectionUnionType) {
            /** @var array<ReflectionNamedType> $reflectionNamedTypes */
            $reflectionNamedTypes = $type->getTypes();
            return $reflectionNamedTypes[0]->getName();
        }
        if ($type instanceof ReflectionIntersectionType) {
            throw new MetaprogrammingException(
                sprintf(
                    'Intersection type not supported for parameter "%s". Please provide a preset value for it',
                    $parameter->getName()
                )
            );
        }
        return null;
    }
}
