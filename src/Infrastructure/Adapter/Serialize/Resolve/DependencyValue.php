<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

use function array_key_exists;
use function class_exists;
use function enum_exists;
use function Serendipity\Type\Cast\toArray;

class DependencyValue extends TypeMatched
{
    /**
     * @throws ReflectionException
     */
    protected function resolveReflectionParameterType(?ReflectionType $type, mixed $value): ?Value
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolveNamedType($type, $value),
            $type instanceof ReflectionUnionType => $this->resolveUnionType($type, $value),
            $type instanceof ReflectionIntersectionType => $this->resolveIntersectionType($type, $value),
            default => null,
        };
    }

    /**
     * @throws ReflectionException
     */
    protected function resolveNamedType(ReflectionNamedType $type, mixed $value): ?Value
    {
        $builtin = $type->isBuiltin();
        $class = $type->getName();
        if ($builtin || ! class_exists($class) || enum_exists($class)) {
            return null;
        }
        if ($value instanceof $class) {
            return new Value($value);
        }
        return $this->resolveNamedTypeClass($class, $value);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveNamedTypeClass(string $class, mixed $value): ?Value
    {
        $target = $this->target($class);
        $set = $this->convertValueToSet($target->parameters, $value);
        return new Value($this->build($class, $set, $this->path));
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    protected function convertValueToSet(array $parameters, mixed $value): Set
    {
        $input = toArray($value, [$value]);
        $values = [];
        foreach ($parameters as $index => $parameter) {
            $name = $this->casedName($parameter);
            if (array_key_exists($name, $input)) {
                $values[$name] = $input[$name];
                continue;
            }
            if (array_key_exists($index, $input)) {
                $values[$name] = $input[$index];
            }
        }
        return Set::createFrom($values);
    }
}
