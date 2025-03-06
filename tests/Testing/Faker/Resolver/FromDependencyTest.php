<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Faker\Resolver;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Reflective\CaseConvention;
use Serendipity\Domain\Support\Reflective\Factory\Target;
use Serendipity\Domain\Support\Set;
use Serendipity\Test\Testing\Stub\Builtin;
use Serendipity\Test\Testing\Stub\Complex;
use Serendipity\Test\Testing\Stub\EntityStub;
use Serendipity\Test\Testing\Stub\Intersection;
use Serendipity\Test\Testing\Stub\Type\Intersected;
use Serendipity\Test\Testing\Stub\Union;
use Serendipity\Test\Testing\Stub\Variety;
use Serendipity\Testing\Faker\Resolver\FromDependency;

/**
 * @internal
 */
final class FromDependencyTest extends TestCase
{
    public function testShouldResolveClassDependency(): void
    {
        $resolver = new FromDependency(CaseConvention::SNAKE);
        $target = Target::createFrom(Complex::class);
        $parameters = $target->getReflectionParameters();

        [0 => $entityParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($entityParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsArray($value->content);
        $this->assertArrayHasKey('id', $value->content);
        $this->assertArrayHasKey('price', $value->content);
        $this->assertArrayHasKey('name', $value->content);
        $this->assertArrayHasKey('is_active', $value->content);
    }

    public function testShouldNotResolveBuiltinType(): void
    {
        $resolver = new FromDependency(CaseConvention::SNAKE);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        [0 => $stringParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($stringParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldNotResolveEnumType(): void
    {
        $resolver = new FromDependency(CaseConvention::SNAKE);
        $target = Target::createFrom(EntityStub::class);
        $parameters = $target->getReflectionParameters();

        [8 => $enumParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($enumParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldNotResolveIntersectionType(): void
    {
        $resolver = new FromDependency(CaseConvention::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [1 => $intersectionParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($intersectionParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldResolveUnionTypeWithFirstEligibleType(): void
    {
        $resolver = new FromDependency(CaseConvention::SNAKE);
        $target = Target::createFrom(Union::class);
        $parameters = $target->getReflectionParameters();

        [2 => $nativeUnionParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($nativeUnionParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsArray($value->content);
    }

    public function testShouldFallbackToNextResolverForNonExistingClass(): void
    {
        $resolver = new FromDependency(CaseConvention::SNAKE);
        $target = Target::createFrom(Intersection::class);
        $parameters = $target->getReflectionParameters();

        [0 => $intersectedParameter] = $parameters;

        $set = Set::createFrom(['intersected' => new Intersected()]);
        $value = $resolver->resolve($intersectedParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldReturnNullForParameterWithoutType(): void
    {
        $resolver = new FromDependency(CaseConvention::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [3 => $whateverParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($whateverParameter, $set);

        $this->assertNull($value);
    }
}
