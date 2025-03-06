<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Resolver;

use DateMalformedStringException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Domain\Exception\SchemaException;
use Serendipity\Domain\Support\Reflective\Attributes\Define;
use Serendipity\Domain\Support\Reflective\Attributes\Managed;
use Serendipity\Domain\Support\Reflective\Definition\Type;
use Serendipity\Domain\Support\Reflective\Definition\TypeExtended;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Testing\Extension\ManagedExtension;
use Serendipity\Testing\Faker\Resolver;
use Throwable;

final class FromType extends Resolver
{
    use MakeExtension;
    use ManagedExtension;

    /**
     * @throws DateMalformedStringException
     * @throws GeneratingException
     */
    public function resolve(ReflectionParameter $parameter, Set $preset): ?Value
    {
        $type = $this->extractType($parameter);
        if ($type === null) {
            return parent::resolve($parameter, $preset);
        }

        return $this->resolveByAttributes($parameter)
            ?? $this->resolveByType($type)
            ?? $this->resolveByFormat($type)
            ?? parent::resolve($parameter, $preset);
    }

    private function extractType(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }
        if ($type instanceof ReflectionUnionType) {
            /** @var array<ReflectionNamedType> $reflectionNamedTypes */
            $reflectionNamedTypes = $type->getTypes();
            $index = $this->generator->numberBetween(0, count($reflectionNamedTypes) - 1);
            return $reflectionNamedTypes[$index]->getName();
        }
        if ($type instanceof ReflectionIntersectionType) {
            throw new SchemaException(
                sprintf(
                    'Intersection type not supported for parameter "%s". Please provide a preset value for it',
                    $parameter->getName()
                )
            );
        }
        return null;
    }

    /**
     * @throws GeneratingException
     */
    private function resolveByAttributes(ReflectionParameter $parameter): ?Value
    {
        $attributes = $parameter->getAttributes();
        if (empty($attributes)) {
            return null;
        }
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            $resolved = match (true) {
                $instance instanceof Managed => $this->resolveManaged($instance),
                $instance instanceof Define => $this->resolveDefine($instance),
                default => null,
            };
            if ($resolved !== null) {
                return $resolved;
            }
        }
        return null;
    }

    /**
     * @throws DateMalformedStringException
     */
    private function resolveByType(string $type): ?Value
    {
        return match ($type) {
            'int' => new Value($this->generator->numberBetween(1, 100)),
            'string' => new Value($this->generator->word()),
            'bool' => new Value($this->generator->boolean()),
            'float' => new Value($this->generator->randomFloat(2, 1, 100)),
            'array' => new Value($this->generator->words()),
            DateTimeImmutable::class => new Value(new DateTimeImmutable($this->now())),
            DateTime::class => new Value(new DateTime($this->now())),
            default => null,
        };
    }

    private function resolveByFormat(string $type): ?Value
    {
        try {
            return new Value($this->generator->format($type));
        } catch (Throwable) {
            return null;
        }
    }

    private function now(): string
    {
        return $this->generator->dateTime()->format(DateTimeInterface::ATOM);
    }

    /**
     * @throws GeneratingException
     */
    public function resolveManaged(Managed $instance): ?Value
    {
        return match ($instance->management) {
            'id' => new Value($this->managed()->id()),
            'now' => new Value($this->managed()->now()),
            default => null,
        };
    }

    private function resolveDefine(Define $instance): ?Value
    {
        $types = $instance->types;
        foreach ($types as $type) {
            $resolved = match (true) {
                $type instanceof Type => $this->resolveByFormat($type->value),
                $type instanceof TypeExtended => $type->fake($this),
                default => null,
            };
            if ($resolved !== null) {
                return $resolved;
            }
        }
        return null;
    }
}
