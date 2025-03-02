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
        $name = $this->name($parameter);
        if ($set->has($name)) {
            return $this->resolveTypeMatched($parameter, $set);
        }
        return parent::resolve($parameter, $set);
    }

    protected function resolveTypeMatched(ReflectionParameter $parameter, Set $set): Value
    {
        $field = $this->name($parameter);
        $value = $set->get($field);
        $type = $parameter->getType();
        $resolved = $this->resolveReflectionParameterType($type, $value);
        return $resolved ?? parent::resolve($parameter, $set);
    }

    private function resolveReflectionParameterType(?ReflectionType $type, mixed $value): ?Value
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolveNamedType($type, $value),
            $type instanceof ReflectionUnionType => $this->resolveUnionType($type, $value),
            $type instanceof ReflectionIntersectionType => $this->resolveIntersectionType($type, $value),
            default => new Value($value),
        };
    }

    private function resolveNamedType(ReflectionNamedType $type, mixed $value): ?Value
    {
        $builtin = $type->isBuiltin();
        $actual = $type->getName();
        return ($builtin)
            ? $this->resolveBuiltinType($actual, $value)
            : $this->resolveInstanceType($actual, $value);
    }

    private function resolveBuiltinType(string $actual, mixed $value): ?Value
    {
        $current = $this->detectType($value);
        return ($actual === $current)
            ? new Value($value)
            : null;
    }

    private function resolveInstanceType(string $actual, mixed $value): ?Value
    {
        return ($value instanceof $actual)
            ? new Value($value)
            : null;
    }

    private function resolveUnionType(ReflectionUnionType $type, mixed $value): ?Value
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

    private function resolveIntersectionType(ReflectionIntersectionType $type, mixed $value): ?Value
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
