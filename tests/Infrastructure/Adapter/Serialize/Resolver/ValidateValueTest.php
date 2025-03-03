<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serialize\Resolver;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\ValidateValue;
use Serendipity\Test\Testing\Stub\Builtin;

final class ValidateValueTest extends TestCase
{
    public function testShouldValidateValueRequired(): void
    {
        $chain = new ValidateValue(path: ['string']);
        $target = $chain->extractTarget(Builtin::class);
        $parameters = $target->parameters;

        $this->assertCount(6, $parameters);

        $set = Set::createFrom([]);

        [0 => $string] = $parameters;

        $value = $chain->resolve($string, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals("The value for 'string' is required and was not given.", $value->content->message);
    }

    public function testShouldValidateValueMismatch(): void
    {
        $chain = new ValidateValue(path: ['int']);
        $target = $chain->extractTarget(Builtin::class);
        $parameters = $target->parameters;

        $this->assertCount(6, $parameters);

        $set = Set::createFrom(['int' => '10']);

        [1 => $int] = $parameters;

        $value = $chain->resolve($int, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals(
            "The value for 'int' must be of type 'int' and 'string' was given.",
            $value->content->message
        );
    }
}
