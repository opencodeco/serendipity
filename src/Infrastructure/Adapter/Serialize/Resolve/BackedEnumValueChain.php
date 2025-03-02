<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use BackedEnum;
use ReflectionEnum;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

class BackedEnumValueChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $parameterType = $parameter->getType();
        if (! $parameterType instanceof ReflectionNamedType) {
            return parent::resolve($parameter, $set);
        }
        $enum = $parameterType->getName();
        if ($this->isNotEnum($enum)) {
            return parent::resolve($parameter, $set);
        }
        $value = $set->get($this->name($parameter));
        if ($value instanceof $enum) {
            return new Value($value);
        }
        return $this->resolveEnumValue($enum, $parameter, $set);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveEnumValue(
        BackedEnum|string $enum,
        ReflectionParameter $parameter,
        Set $values
    ): Value {
        $value = $values->get($this->name($parameter));
        if (! is_int($value) && ! is_string($value)) {
            return parent::resolve($parameter, $values);
        }
        $valueType = $this->detectType($value);
        /** @phpstan-ignore argument.type */
        $reflectionEnum = new ReflectionEnum($enum);
        $backingType = $reflectionEnum->getBackingType()?->getName();
        if ($backingType === $valueType) {
            return new Value($enum::from($value));
        }
        return parent::resolve($parameter, $values);
    }

    private function isNotEnum(string $enum): bool
    {
        return ! is_subclass_of($enum, BackedEnum::class) || ! enum_exists($enum);
    }
}
