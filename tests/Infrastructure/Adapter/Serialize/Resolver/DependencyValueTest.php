<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serialize\Resolver;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Support\Set;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\DependencyValue;
use Serendipity\Infrastructure\Adapter\Serialize\Target;
use Serendipity\Test\Testing\Stub\Builtin;
use Serendipity\Test\Testing\Stub\Command;
use Serendipity\Test\Testing\Stub\Complex;
use Serendipity\Test\Testing\Stub\EntityStub;
use Serendipity\Test\Testing\Stub\Intersection;
use Serendipity\Test\Testing\Stub\Native;
use Serendipity\Test\Testing\Stub\NoConstructor;
use Serendipity\Test\Testing\Stub\Union;
use Serendipity\Testing\Extension\FakerExtension;
use stdClass;

final class DependencyValueTest extends TestCase
{
    use MakeExtension;
    use FakerExtension;

    #[TestWith(['2021-01-01'])]
    #[TestWith([new DateTimeImmutable('2021-01-01')])]
    public function testShouldHandleDependency(mixed $value): void
    {
        $resolver = new DependencyValue();
        $target = Target::createFrom(Command::class);
        $parameters = $target->parameters();

        $set = Set::createFrom(['signup_date' => $value]);

        [
            2 => $signupDate,
            13 => $dob,
        ] = $parameters;

        $resolved = $resolver->resolve($signupDate, $set);
        $this->assertInstanceOf(DateTimeImmutable::class, $resolved->content);

        $resolved = $resolver->resolve($dob, $set);
        $this->assertInstanceOf(NotResolved::class, $resolved->content);
    }

    public function testShouldHandleDependencyComplex(): void
    {
        $resolver = new DependencyValue();
        $target = Target::createFrom(Complex::class);
        $parameters = $target->parameters();

        $generator = $this->generator();
        $set = Set::createFrom([
            'entity' => [
                'id' => $generator->numberBetween(1, 100),
                'price' => $generator->randomFloat(),
                'name' => $generator->name(),
                'is_active' => $generator->boolean(),
                'more' => new NoConstructor(),
            ],
            'native' => [
                'callable' => fn () => null,
                'std_class' => new stdClass(),
                'date_time_immutable' => new DateTimeImmutable(),
                'date_time' => '2021-01-01',
                'date_time_interface' => new DateTime('2021-01-01'),
            ],
            'builtin' => [
                $generator->word(),
                $generator->numberBetween(1, 100),
                $generator->randomFloat(),
                $generator->boolean(),
                $generator->words(),
                null,
            ],
        ]);

        [
            $entity,
            $native,
            $builtin,
        ] = $parameters;

        $resolved = $resolver->resolve($entity, $set);
        $this->assertInstanceOf(EntityStub::class, $resolved->content);

        $resolved = $resolver->resolve($native, $set);
        $this->assertInstanceOf(Native::class, $resolved->content);

        $resolved = $resolver->resolve($builtin, $set);
        $this->assertInstanceOf(Builtin::class, $resolved->content);
    }

    public function testShouldHandleDependencyUnion(): void
    {
        $resolver = new DependencyValue();
        $target = Target::createFrom(Union::class);
        $parameters = $target->parameters();

        $set = Set::createFrom(['native' => new stdClass()]);

        [2 => $native] = $parameters;

        $resolved = $resolver->resolve($native, $set);
        $this->assertInstanceOf(DateTimeInterface::class, $resolved->content);
    }

    public function testShouldHandleDependencyIntersection(): void
    {
        $resolver = new DependencyValue();
        $target = Target::createFrom(Intersection::class);
        $parameters = $target->parameters();

        $set = Set::createFrom(['intersected' => null]);

        [$intersected] = $parameters;

        $resolved = $resolver->resolve($intersected, $set);
        $this->assertInstanceOf(NotResolved::class, $resolved->content);
    }
}
