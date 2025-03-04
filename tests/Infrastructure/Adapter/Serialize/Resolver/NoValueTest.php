<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serialize\Resolver;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\NoValue;
use Serendipity\Infrastructure\Adapter\Serialize\Target;
use Serendipity\Test\Testing\Stub\NullableAndOptional;

final class NoValueTest extends TestCase
{
    public function testNoValueSuccessfully(): void
    {
        $resolver = new NoValue();
        $target = Target::createFrom(NullableAndOptional::class);
        $parameters = $target->parameters();

        $this->assertCount(3, $parameters);

        $empty = Set::createFrom([]);

        [
            $nullable,
            $union,
            $optional,
        ] = $parameters;

        $value = $resolver->resolve($nullable, $empty);
        $this->assertNull($value->content);

        $value = $resolver->resolve($union, $empty);
        $this->assertNull($value->content);

        $value = $resolver->resolve($optional, $empty);
        $this->assertSame(10, $value->content);
    }
}
