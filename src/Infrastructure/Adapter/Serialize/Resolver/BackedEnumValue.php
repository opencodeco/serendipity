<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use BackedEnum;
use ReflectionEnum;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\ResolverTyped;

use function is_int;
use function is_string;
use function is_subclass_of;

class BackedEnumValue extends ResolverTyped
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $type = $parameter->getType();
        $value = $set->get($this->formatParameterName($parameter));
        $resolved = $this->resolveReflectionParameterType($type, $value);
        return $resolved ?? parent::resolve($parameter, $set);
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
        $valueType = $this->detectValueType($value);
        /** @phpstan-ignore argument.type */
        $reflectionEnum = new ReflectionEnum($enum);
        $backingType = $reflectionEnum->getBackingType()?->getName();
        if ($backingType !== $valueType) {
            return null;
        }
        return new Value($enum::from($value));
    }
}
