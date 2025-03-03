<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

class TypeMatched extends Chain
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $field = $this->casedName($parameter);
        if (! $set->has($field)) {
            return parent::resolve($parameter, $set);
        }
        $type = $parameter->getType();
        $value = $set->get($field);
        $resolved = $this->resolveReflectionParameterType($type, $value);
        return $resolved ?? parent::resolve($parameter, $set);
    }

    protected function resolveReflectionParameterType(?ReflectionType $type, mixed $value): ?Value
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolveNamedType($type, $value),
            $type instanceof ReflectionUnionType => $this->resolveUnionType($type, $value),
            $type instanceof ReflectionIntersectionType => $this->resolveIntersectionType($type, $value),
            default => new Value($value),
        };
    }

    protected function resolveNamedType(ReflectionNamedType $type, mixed $value): ?Value
    {
        $builtin = $type->isBuiltin();
        $actual = $type->getName();
        return ($builtin)
            ? $this->resolveNamedTypeBuiltin($actual, $value)
            : $this->resolveNamedTypeInstanceOf($actual, $value);
    }

    private function resolveNamedTypeBuiltin(string $actual, mixed $value): ?Value
    {
        $current = $this->detectType($value);
        return ($actual === $current)
            ? new Value($value)
            : null;
    }

    private function resolveNamedTypeInstanceOf(string $actual, mixed $value): ?Value
    {
        return ($value instanceof $actual)
            ? new Value($value)
            : null;
    }

    protected function resolveUnionType(ReflectionUnionType $type, mixed $value): ?Value
    {
        $types = $type->getTypes();
        foreach ($types as $nested) {
            $resolved = $this->resolveReflectionParameterType($nested, $value);
            if ($resolved !== null) {
                return $resolved;
            }
        }
        return null;
    }

    protected function resolveIntersectionType(ReflectionIntersectionType $type, mixed $value): ?Value
    {
        $types = $type->getTypes();
        foreach ($types as $nested) {
            if ($this->resolveReflectionParameterType($nested, $value) === null) {
                return null;
            }
        }
        return new Value($value);
    }
}
