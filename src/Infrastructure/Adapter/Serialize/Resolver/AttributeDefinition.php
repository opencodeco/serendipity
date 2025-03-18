<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use DateMalformedStringException;
use DateTimeImmutable;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Support\Reflective\Attribute\Define;
use Serendipity\Domain\Support\Reflective\Attribute\Managed;
use Serendipity\Domain\Support\Reflective\Attribute\Pattern;
use Serendipity\Domain\Support\Reflective\Definition\Type;
use Serendipity\Domain\Support\Reflective\Definition\TypeExtended;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\ResolverTyped;

use function Serendipity\Type\Cast\stringify;

final class AttributeDefinition extends ResolverTyped
{
    /**
     * @throws DateMalformedStringException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $type = $this->formatTypeName($parameter->getType());
        if ($type === null) {
            return parent::resolve($parameter, $set);
        }
        $field = $this->formatParameterName($parameter);
        $value = $set->get($field);
        return $this->resolveByAttributes($parameter, $value)
            ?? parent::resolve($parameter, $set);
    }

    /**
     * @throws DateMalformedStringException
     */
    private function resolveByAttributes(ReflectionParameter $parameter, mixed $value): ?Value
    {
        $attributes = $parameter->getAttributes();
        if (empty($attributes)) {
            return null;
        }
        $resolved = null;
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            $resolved = match (true) {
                $instance instanceof Managed => $this->resolveManaged($instance, $value),
                $instance instanceof Define => $this->resolveDefine($instance, $value),
                $instance instanceof Pattern => $this->resolvePattern($instance, $value, $parameter->getType()),
                default => null,
            };
            if ($resolved !== null) {
                break;
            }
        }
        return $resolved;
    }

    /**
     * @throws DateMalformedStringException
     */
    private function resolveManaged(Managed $instance, mixed $value): ?Value
    {
        return match ($instance->management) {
            'id' => new Value($value),
            'now' => new Value(new DateTimeImmutable($value)),
            default => null,
        };
    }

    private function resolveDefine(Define $instance, mixed $value): ?Value
    {
        $types = $instance->types;
        $callback = fn (?Value $carry, Type|TypeExtended $type): ?Value => $carry ?? match (true) {
            $type instanceof Type => $this->resolveDefineType($type, $value),
            $type instanceof TypeExtended => new Value($type->build($value)),
        };
        return array_reduce($types, $callback);
    }

    private function resolvePattern(Pattern $instance, mixed $value, ?ReflectionType $type): ?Value
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolvePatternFromNamedType($instance, $value, $type),
            $type instanceof ReflectionUnionType => $this->resolvePatternFromUnionType($instance, $value, $type),
            default => null,
        };
    }

    private function resolvePatternFromNamedType(Pattern $instance, mixed $value, ReflectionNamedType $type): ?Value
    {
        $value = stringify($value);
        if (preg_match($instance->pattern, $value) !== 1) {
            return null;
        }
        $name = $type->getName();
        $content = match ($name) {
            'int' => (int) $value,
            'float' => (float) $value,
            default => $value,
        };
        return new Value($content);
    }

    private function resolvePatternFromUnionType(
        Pattern $instance,
        mixed $value,
        ReflectionUnionType $unionType
    ): ?Value {
        $types = $unionType->getTypes();
        $resolved = null;
        foreach ($types as $type) {
            $resolved = $this->resolvePattern($instance, $value, $type);
            if ($resolved !== null) {
                break;
            }
        }
        return $resolved;
    }

    private function resolveDefineType(Type $type, mixed $value): ?Value
    {
        $content = match ($type) {
            Type::EMOJI => stringify($value),
            default => $value,
        };
        return new Value($content);
    }
}
