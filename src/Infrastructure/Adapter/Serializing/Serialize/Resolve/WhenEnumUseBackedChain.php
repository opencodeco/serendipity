<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve;

use BackedEnum;
use ReflectionEnum;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;

class WhenEnumUseBackedChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, Values $values): Value
    {
        $parameterType = $parameter->getType();
        if (! $parameterType instanceof ReflectionNamedType) {
            return parent::resolve($parameter, $values);
        }
        $enum = $parameterType->getName();
        if (! is_subclass_of($enum, BackedEnum::class) || ! enum_exists($enum)) {
            return parent::resolve($parameter, $values);
        }
        $value = $values->get($this->name($parameter));
        if ($value instanceof $enum) {
            return new Value($value);
        }
        $reflectionEnum = new ReflectionEnum($enum);
        $backingType = $reflectionEnum->getBackingType()?->getName();
        $valueType = $this->type($value);
        if ($backingType !== $valueType) {
            return parent::resolve($parameter, $values);
        }
        return new Value($enum::from($value));
    }
}
