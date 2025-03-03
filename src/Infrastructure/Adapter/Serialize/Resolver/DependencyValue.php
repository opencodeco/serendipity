<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use ReflectionException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Exception\AdapterException;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\ResolverTyped;
use Serendipity\Infrastructure\Adapter\Serialize\Target;

use function array_key_exists;
use function class_exists;
use function count;
use function enum_exists;
use function Serendipity\Type\Cast\toArray;

class DependencyValue extends ResolverTyped
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $field = $this->casedName($parameter);
        if (! $set->has($field)) {
            return parent::resolve($parameter, $set);
        }
        return $this->resolveDependencyValue($parameter, $set, $field);
    }

    /**
     * @throws ReflectionException
     */
    final protected function resolveDependencyValue(
        ReflectionParameter $parameter,
        Set $set,
        string $field
    ): Value {
        $type = $parameter->getType();
        $value = $set->get($field);
        $resolved = $this->resolveReflectionParameterType($type, $value);
        return $resolved ?? parent::resolve($parameter, $set);
    }

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
        $target = Target::createFrom($class);
        $parameters = $target->parameters;
        if ($value !== null && count($parameters) === 0) {
            return null;
        }
        $set = $this->convertValueToSet($parameters, $value);
        try {
            $content = $this->make($class, $set, $this->path);
            return new Value($content);
        } catch (AdapterException $exception) {
            return $this->notResolved($exception->getUnresolved(), $value);
        }
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
