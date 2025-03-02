<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use BackedEnum;
use ReflectionEnum;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

class BackedEnumValue extends TypeMatched
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $field = $this->name($parameter);
        $value = $set->get($field);
        $type = $parameter->getType();
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
        $enum = $type->getName();
        return match (true) {
            $value instanceof $enum => new Value($value),
            is_subclass_of($enum, BackedEnum::class) => $this->resolveNamedTypeEnum($enum, $value),
            default => null,
        };
    }

    /**
     * @throws ReflectionException
     */
    private function resolveNamedTypeEnum(BackedEnum|string $enum, mixed $value): ?Value
    {
        if (! is_int($value) && ! is_string($value)) {
            return null;
        }
        $valueType = $this->detectType($value);
        /** @phpstan-ignore argument.type */
        $reflectionEnum = new ReflectionEnum($enum);
        $backingType = $reflectionEnum->getBackingType()?->getName();
        if ($backingType !== $valueType) {
            return null;
        }
        return new Value($enum::from($value));
    }
}
