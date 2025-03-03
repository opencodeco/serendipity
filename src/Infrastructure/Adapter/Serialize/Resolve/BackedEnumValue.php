<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use BackedEnum;
use ReflectionEnum;
use ReflectionException;
use ReflectionNamedType;
use Serendipity\Domain\Support\Value;

class BackedEnumValue extends DependencyValue
{
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
