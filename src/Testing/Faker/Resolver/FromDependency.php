<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Resolver;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Exception\SchemaException;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Testing\Faker\Resolver;
use Throwable;

final class FromDependency extends Resolver
{
    public function resolve(ReflectionParameter $parameter, Set $preset): ?Value
    {
        $type = $parameter->getType();
        if ($type instanceof ReflectionIntersectionType) {
            throw new SchemaException(
                sprintf(
                    'Intersection type not supported for parameter "%s". Please provide a preset value for it',
                    $parameter->getName()
                )
            );
        }
        $resolved = $this->resolveReflectionParameterType($type, $preset);
        return $resolved ?? parent::resolve($parameter, $preset);
    }

    private function resolveReflectionParameterType(?ReflectionType $type, Set $preset): ?Value
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolveFromNamedType($type, $preset),
            $type instanceof ReflectionUnionType => $this->resolveFromUnionType($type, $preset),
            default => null,
        };
    }

    private function resolveFromNamedType(ReflectionNamedType $type, Set $preset): ?Value
    {
        $class = $type->getName();
        if ($this->isEligibleForDependency($class, $type)) {
            return null;
        }
        try {
            $resolved = $this->fake($class, $preset->toArray());
            return new Value($resolved->toArray());
        } catch (Throwable) {
            return null;
        }
    }

    private function resolveFromUnionType(ReflectionUnionType $unionType, Set $preset): ?Value
    {
        $types = $unionType->getTypes();
        foreach ($types as $type) {
            $resolved = $this->resolveReflectionParameterType($type, $preset);
            if ($resolved !== null) {
                return $resolved;
            }
        }
        return null;
    }

    private function isEligibleForDependency(string $class, ReflectionNamedType $type): bool
    {
        return ! class_exists($class) || $type->isBuiltin() || enum_exists($class);
    }
}
