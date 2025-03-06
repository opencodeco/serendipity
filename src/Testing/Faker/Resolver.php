<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Serendipity\Domain\Exception\SchemaException;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

abstract class Resolver extends Faker
{
    protected ?Resolver $previous = null;

    final public function then(Resolver $resolver): Resolver
    {
        $resolver->previous($this);
        return $resolver;
    }

    public function resolve(ReflectionParameter $parameter, Set $preset): ?Value
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

    protected function extractType(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }
        if ($type instanceof ReflectionUnionType) {
            /** @var array<ReflectionNamedType> $reflectionNamedTypes */
            $reflectionNamedTypes = $type->getTypes();
            $index = $this->generator->numberBetween(0, count($reflectionNamedTypes) - 1);
            return $reflectionNamedTypes[$index]->getName();
        }
        if ($type instanceof ReflectionIntersectionType) {
            throw new SchemaException(
                sprintf(
                    'Intersection type not supported for parameter "%s". Please provide a preset value for it',
                    $parameter->getName()
                )
            );
        }
        return null;
    }
}
