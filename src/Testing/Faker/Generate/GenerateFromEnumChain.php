<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Generate;

use BackedEnum;
use Random\RandomException;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Set;

final class GenerateFromEnumChain extends Chain
{
    /**
     * @throws RandomException
     */
    public function resolve(ReflectionParameter $parameter, ?Set $preset = null): ?Value
    {
        $parameterType = $parameter->getType();
        if (! $parameterType instanceof ReflectionNamedType) {
            return parent::resolve($parameter, $preset);
        }
        $enum = $parameterType->getName();
        if ($this->isNotEnum($enum)) {
            return parent::resolve($parameter, $preset);
        }
        return $this->resolveEnumValue($enum, $parameter, $preset);
    }

    private function isNotEnum(string $enum): bool
    {
        return ! is_subclass_of($enum, BackedEnum::class) || ! enum_exists($enum);
    }

    /**
     * @throws RandomException
     */
    private function resolveEnumValue(string $enum, ReflectionParameter $parameter, ?Set $preset): ?Value
    {
        if (! is_subclass_of($enum, BackedEnum::class)) {
            return parent::resolve($parameter, $preset);
        }

        $enumValues = $enum::cases();
        if (empty($enumValues)) {
            return parent::resolve($parameter, $preset);
        }

        $randomValue = $enumValues[random_int(0, count($enumValues) - 1)]->value;
        return new Value($randomValue);
    }
}
