<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Resolver;

use BackedEnum;
use Random\RandomException;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Testing\Faker\Resolver;

final class FromEnum extends Resolver
{
    /**
     * @throws RandomException
     */
    public function resolve(ReflectionParameter $parameter, Set $preset): ?Value
    {
        $type = $parameter->getType();
        if (! $type instanceof ReflectionNamedType) {
            return parent::resolve($parameter, $preset);
        }
        $enum = $type->getName();
        return enum_exists($enum)
            ? $this->resolveEnumValue($enum, $parameter, $preset)
            : parent::resolve($parameter, $preset);
    }

    /**
     * @throws RandomException
     */
    private function resolveEnumValue(string $enum, ReflectionParameter $parameter, Set $preset): ?Value
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
