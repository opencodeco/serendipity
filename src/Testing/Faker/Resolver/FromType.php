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
use Serendipity\Domain\Exception\MetaprogrammingException;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Testing\Faker\Resolver;
use Throwable;

final class FromType extends Resolver
{
    /**
     * @throws DateMalformedStringException
     */
    public function resolve(ReflectionParameter $parameter, ?Set $preset = null): ?Value
    {
        $type = $this->extractType($parameter);
        if ($type === null) {
            return parent::resolve($parameter, $preset);
        }
        try {
            return new Value($this->generator->format($type));
        } catch (Throwable) {
        }
        return match ($type) {
            'int' => new Value($this->generator->numberBetween(1, 100)),
            'string' => new Value($this->generator->word()),
            'bool' => new Value($this->generator->boolean()),
            'float' => new Value($this->generator->randomFloat(2, 1, 100)),
            'array' => new Value($this->generator->words()),
            DateTimeImmutable::class => new Value(new DateTimeImmutable($this->now())),
            DateTime::class => new Value(new DateTime($this->now())),
            default => parent::resolve($parameter, $preset),
        };
    }

    private function now(): string
    {
        return $this->generator->dateTime()->format(DateTimeInterface::ATOM);
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
            throw new MetaprogrammingException(
                sprintf(
                    'Intersection type not supported for parameter "%s". Please provide a preset value for it',
                    $parameter->getName()
                )
            );
        }
        return null;
    }
}
