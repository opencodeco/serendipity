<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize\Resolve;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Reflective\Attribute\Managed;
use Serendipity\Domain\Support\Reflective\Attribute\Pattern;
use Serendipity\Domain\Support\Reflective\Definition\Type;
use Serendipity\Domain\Support\Reflective\Definition\TypeExtended;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;
use Serendipity\Infrastructure\Adapter\Support\AttributeAdapter;

use function Serendipity\Type\Cast\stringify;

class AttributeChain extends Chain
{
    use AttributeAdapter;

    /**
     * @throws DateMalformedStringException
     */
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $type = $this->formatTypeName($parameter->getType());
        if ($type === null) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveByAttributes($parameter, $value)
            ?? parent::resolve($parameter, $value);
    }

    protected function resolveManaged(Managed $instance, mixed $value): ?Value
    {
        return match ($instance->management) {
            'id' => new Value($value),
            'timestamp' => new Value(
                $value instanceof DateTimeImmutable
                    ? $value->format(DateTimeInterface::ATOM)
                    : $value
            ),
            default => null,
        };
    }

    protected function resolveDefineType(Type $type, mixed $value): Value
    {
        $content = match ($type) {
            Type::EMOJI => stringify($value),
            default => $value,
        };
        return new Value($content);
    }

    protected function resolveDefineTypeExtended(TypeExtended $type, mixed $value): Value
    {
        return new Value(
            $type->demolish(
                $value,
                /* @phpstan-ignore argument.type, argument.templateType */
                fn (object $instance) => $this->demolish($instance)
            )
        );
    }

    protected function resolvePatternFromNamedType(Pattern $instance, mixed $value, ReflectionNamedType $type): ?Value
    {
        return new Value($value);
    }
}
