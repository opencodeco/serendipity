<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serialize\Resolve;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\NoValue;
use Serendipity\Test\Testing\Stub\NullableAndOptional;

final class NoValueTest extends TestCase
{
    public function testNoValueSuccessfully(): void
    {
        $noValue = new NoValue();
        $target = $noValue->target(NullableAndOptional::class);
        $parameters = $target->parameters;

        $this->assertCount(3, $parameters);

        $empty = Set::createFrom([]);

        [
            $nullable,
            $union,
            $optional,
        ] = $parameters;

        $value = $noValue->resolve($nullable, $empty);
        $this->assertNull($value->content);

        $value = $noValue->resolve($union, $empty);
        $this->assertNull($value->content);

        $value = $noValue->resolve($optional, $empty);
        $this->assertSame(10, $value->content);
    }
}
