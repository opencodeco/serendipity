<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serialize\Resolver;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\TypeMatched;
use Serendipity\Test\Testing\Stub\Builtin;
use Serendipity\Test\Testing\Stub\EntityStub;
use Serendipity\Test\Testing\Stub\Intersection;
use Serendipity\Test\Testing\Stub\Native;
use Serendipity\Test\Testing\Stub\NoConstructor;
use Serendipity\Test\Testing\Stub\NotNative;
use Serendipity\Test\Testing\Stub\Stub;
use Serendipity\Test\Testing\Stub\Type\BackedEnumeration;
use Serendipity\Test\Testing\Stub\Type\Enumeration;
use Serendipity\Test\Testing\Stub\Type\Intersected;
use Serendipity\Test\Testing\Stub\Union;
use Serendipity\Test\Testing\Stub\Variety;
use stdClass;

final class TypeMatchedTest extends TestCase
{
    public function testTypeMatchedBuiltinSuccessfully(): void
    {
        $typeMatched = new TypeMatched();
        $target = $typeMatched->extractTarget(Builtin::class);
        $parameters = $target->parameters;

        $this->assertCount(6, $parameters);

        $set = Set::createFrom([
            'string' => 'string',
            'int' => 10,
            'float' => 10.1,
            'bool' => true,
            'array' => ['a', 'b', 'c'],
            'null' => null,
        ]);

        [
            $string,
            $int,
            $float,
            $bool,
            $array,
            $null,
        ] = $parameters;

        $value = $typeMatched->resolve($string, $set);
        $this->assertSame('string', $value->content);

        $value = $typeMatched->resolve($int, $set);
        $this->assertSame(10, $value->content);

        $value = $typeMatched->resolve($float, $set);
        $this->assertSame(10.1, $value->content);

        $value = $typeMatched->resolve($bool, $set);
        $this->assertTrue($value->content);

        $value = $typeMatched->resolve($array, $set);
        $this->assertSame(['a', 'b', 'c'], $value->content);

        $value = $typeMatched->resolve($null, $set);
        $this->assertNull($value->content);
    }

    public function testTypeMatchedNativeSuccessfully(): void
    {
        $typeMatched = new TypeMatched();
        $target = $typeMatched->extractTarget(Native::class);
        $parameters = $target->parameters;

        $this->assertCount(5, $parameters);

        $set = Set::createFrom([
            'callable' => fn () => null,
            'std_class' => new stdClass(),
            'date_time_immutable' => new DateTimeImmutable(),
            'date_time' => new DateTime(),
            'date_time_interface' => new DateTime(),
        ]);

        [
            $callable,
            $stdClass,
            $dateTimeImmutable,
        ] = $parameters;

        $value = $typeMatched->resolve($callable, $set);
        $this->assertIsCallable($value->content);

        $value = $typeMatched->resolve($stdClass, $set);
        $this->assertInstanceOf(stdClass::class, $value->content);

        $value = $typeMatched->resolve($dateTimeImmutable, $set);
        $this->assertInstanceOf(DateTimeImmutable::class, $value->content);
    }

    public function testTypeMatchedNotNativeSuccessfully(): void
    {
        $typeMatched = new TypeMatched();
        $target = $typeMatched->extractTarget(NotNative::class);
        $parameters = $target->parameters;

        $this->assertCount(3, $parameters);

        $set = Set::createFrom([
            'backed' => BackedEnumeration::BAZ,
            'enum' => Enumeration::ONE,
            'stub' => new Stub(
                'string',
                10
            )
        ]);

        [
            $backed,
            $enumeration,
            $stub,
        ] = $parameters;

        $value = $typeMatched->resolve($backed, $set);
        $this->assertEquals(BackedEnumeration::BAZ, $value->content);

        $value = $typeMatched->resolve($enumeration, $set);
        $this->assertEquals(Enumeration::ONE, $value->content);

        $value = $typeMatched->resolve($stub, $set);
        $this->assertInstanceOf(Stub::class, $value->content);
    }

    public function testTypeMatchedIntersectionSuccessfully(): void
    {
        $typeMatched = new TypeMatched();
        $target = $typeMatched->extractTarget(Intersection::class);
        $parameters = $target->parameters;

        $this->assertCount(1, $parameters);

        $set = Set::createFrom([
            'intersected' => new Intersected(),
        ]);

        [$intersected] = $parameters;

        $value = $typeMatched->resolve($intersected, $set);
        $this->assertInstanceOf(Intersected::class, $value->content);
    }

    public function testTypeMatchedUnionSuccessfully(): void
    {
        $typeMatched = new TypeMatched();
        $target = $typeMatched->extractTarget(Union::class);
        $parameters = $target->parameters;

        $this->assertCount(3, $parameters);

        $set = Set::createFrom([
            'builtin' => 23,
            'nullable' => null,
            'native' => new stdClass(),
        ]);

        [
            $builtin,
            $nullable,
            $native,
        ] = $parameters;

        $value = $typeMatched->resolve($builtin, $set);
        $this->assertSame(23, $value->content);

        $value = $typeMatched->resolve($nullable, $set);
        $this->assertNull($value->content);

        $value = $typeMatched->resolve($native, $set);
        $this->assertInstanceOf(stdClass::class, $value->content);
    }

    public function testTypeMatchedShouldNotResolveNoValue(): void
    {
        $typeMatched = new TypeMatched();
        $target = $typeMatched->extractTarget(Intersection::class);
        $parameters = $target->parameters;

        $this->assertCount(1, $parameters);

        $set = Set::createFrom([]);

        [$intersected] = $parameters;
        $value = $typeMatched->resolve($intersected, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
    }

    public function testTypeMatchedShouldNotResolveInvalidForIntersection(): void
    {
        $typeMatched = new TypeMatched();
        $target = $typeMatched->extractTarget(Intersection::class);
        $parameters = $target->parameters;

        $this->assertCount(1, $parameters);

        $set = Set::createFrom([
            'intersected' => new stdClass(),
        ]);

        [$intersected] = $parameters;
        $value = $typeMatched->resolve($intersected, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
    }

    public function testTypeMatchedShouldNotResolveInvalidForUnion(): void
    {
        $typeMatched = new TypeMatched();
        $target = $typeMatched->extractTarget(Union::class);
        $parameters = $target->parameters;

        $set = Set::createFrom([
            'builtin' => true,
        ]);

        [$builtin] = $parameters;

        $value = $typeMatched->resolve($builtin, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
    }

    public function testTypeMatchedShouldResolveVariety(): void
    {
        $typeMatched = new TypeMatched();
        $target = $typeMatched->extractTarget(Variety::class);
        $parameters = $target->parameters;

        $this->assertCount(4, $parameters);

        $set = Set::createFrom([
            'union' => 23,
            'intersection' => new Intersected(),
            'nested' => new EntityStub(
                10,
                10.1,
                'string',
                true,
                new NoConstructor(),
                null,
                null
            ),
            'whatever' => new stdClass(),
        ]);

        [
            $union,
            $intersection,
            $nested,
            $whatever,
        ] = $parameters;

        $value = $typeMatched->resolve($union, $set);
        $this->assertSame(23, $value->content);

        $value = $typeMatched->resolve($intersection, $set);
        $this->assertInstanceOf(Intersected::class, $value->content);

        $value = $typeMatched->resolve($nested, $set);
        $this->assertInstanceOf(EntityStub::class, $value->content);

        $value = $typeMatched->resolve($whatever, $set);
        $this->assertInstanceOf(stdClass::class, $value->content);
    }
}
