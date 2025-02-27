<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve;

use BackedEnum;
use ReflectionEnum;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;

class WhenEnumUseBackedChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Values $values): Value
    {
        $parameterType = $parameter->getType();
        if (! $parameterType instanceof ReflectionNamedType) {
            return parent::resolve($parameter, $values);
        }
        $enum = $parameterType->getName();
        if ($this->isNotEnum($enum)) {
            return parent::resolve($parameter, $values);
        }
        $value = $values->get($this->name($parameter));
        if ($value instanceof $enum) {
            return new Value($value);
        }
        return $this->resolveEnumValue($enum, $parameter, $values);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveEnumValue(
        BackedEnum|string $enum,
        ReflectionParameter $parameter,
        Values $values
    ): Value {
        $value = $values->get($this->name($parameter));
        if (! is_int($value) && ! is_string($value)) {
            return parent::resolve($parameter, $values);
        }
        $valueType = $this->type($value);
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
