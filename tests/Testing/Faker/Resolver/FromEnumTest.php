<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Faker\Resolver;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Reflective\Factory\Target;
use Serendipity\Domain\Support\Reflective\Notation;
use Serendipity\Domain\Support\Set;
use Serendipity\Test\Testing\Stub\DeepDeepDown;
use Serendipity\Test\Testing\Stub\EnumVariety;
use Serendipity\Test\Testing\Stub\NotNative;
use Serendipity\Test\Testing\Stub\Variety;
use Serendipity\Testing\Faker\Resolver\FromEnum;

final class FromEnumTest extends TestCase
{
    public function testShouldResolveBackedEnum(): void
    {
        $resolver = new FromEnum(Notation::SNAKE);
        $target = Target::createFrom(NotNative::class);
        $parameters = $target->getReflectionParameters();

        [0 => $backed] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($backed, $set);

        $this->assertNotNull($value);
        $this->assertContains($value->content, ['foo', 'bar', 'baz']);
    }

    public function testShouldNotResolveNonBackedEnum(): void
    {
        $resolver = new FromEnum(Notation::SNAKE);
        $target = Target::createFrom(NotNative::class);
        $parameters = $target->getReflectionParameters();

        [1 => $enum] = $parameters; // Enum não-backed

        $set = Set::createFrom([]);
        $value = $resolver->resolve($enum, $set);

        $this->assertNull($value);
    }

    public function testShouldResolveEnumInUnionType(): void
    {
        $resolver = new FromEnum(Notation::SNAKE);
        $target = Target::createFrom(EnumVariety::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(4, $parameters);

        [2 => $union] = $parameters; // BackedEnumeration|Enumeration

        $set = Set::createFrom([]);
        $value = $resolver->resolve($union, $set);

        $this->assertNull($value);
    }

    public function testShouldFallbackToNextResolverForNonEnum(): void
    {
        $resolver = new FromEnum(Notation::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [0 => $nonEnum] = $parameters; // int|string

        $set = Set::createFrom([]);
        $value = $resolver->resolve($nonEnum, $set);

        $this->assertNull($value);
    }

    public function testShouldReturnNullForEmptyEnumeration(): void
    {
        $resolver = new FromEnum(Notation::SNAKE);

        $target = Target::createFrom(NotNative::class);
        $parameters = $target->getReflectionParameters();

        [0 => $backed] = $parameters;

        $set = Set::createFrom([]);

        $this->assertNotNull($resolver->resolve($backed, $set));
    }

    public function testShouldNotResolveNotBackedEnum(): void
    {
        $resolver = new FromEnum(Notation::SNAKE);
        $target = Target::createFrom(DeepDeepDown::class);
        $parameters = $target->getReflectionParameters();

        [2 => $emptyEnum] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($emptyEnum, $set);

        $this->assertNull($value);
    }
}
