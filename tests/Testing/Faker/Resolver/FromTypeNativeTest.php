<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Faker\Resolver;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Reflective\Factory\Target;
use Serendipity\Domain\Support\Reflective\Notation;
use Serendipity\Domain\Support\Set;
use Serendipity\Test\Testing\Stub\Native;
use Serendipity\Test\Testing\Stub\Variety;
use Serendipity\Testing\Faker\Resolver\FromTypeNative;

/**
 * @internal
 */
final class FromTypeNativeTest extends TestCase
{
    public function testShouldResolveDateTimeImmutable(): void
    {
        $resolver = new FromTypeNative(Notation::SNAKE);
        $target = Target::createFrom(Native::class);
        $parameters = $target->getReflectionParameters();

        [2 => $dateTimeImmutableParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($dateTimeImmutableParameter, $set);

        $this->assertNotNull($value);
        $this->assertInstanceOf(DateTimeImmutable::class, $value->content);
    }

    public function testShouldResolveDateTime(): void
    {
        $resolver = new FromTypeNative(Notation::SNAKE);
        $target = Target::createFrom(Native::class);
        $parameters = $target->getReflectionParameters();

        [3 => $dateTimeParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($dateTimeParameter, $set);

        $this->assertNotNull($value);
        $this->assertInstanceOf(DateTime::class, $value->content);
    }

    public function testShouldResolveDateTimeInterface(): void
    {
        $resolver = new FromTypeNative(Notation::SNAKE);
        $target = Target::createFrom(Native::class);
        $parameters = $target->getReflectionParameters();

        [4 => $dateTimeInterfaceParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($dateTimeInterfaceParameter, $set);

        $this->assertNotNull($value);
        $this->assertInstanceOf(DateTimeInterface::class, $value->content);
    }

    public function testShouldNotResolveFallbackToNextResolverForNonNativeType(): void
    {
        $resolver = new FromTypeNative(Notation::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [2 => $entityStubParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($entityStubParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldReturnNullForParameterWithoutType(): void
    {
        $resolver = new FromTypeNative(Notation::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [3 => $whateverParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($whateverParameter, $set);

        $this->assertNull($value);
    }
}
