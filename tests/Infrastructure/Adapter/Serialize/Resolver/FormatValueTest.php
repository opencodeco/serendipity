<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serialize\Resolver;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\FormatValue;
use Serendipity\Test\Testing\Stub\Builtin;
use Serendipity\Test\Testing\Stub\EntityStub;
use Serendipity\Test\Testing\Stub\NoConstructor;
use Serendipity\Test\Testing\Stub\Type\Intersected;
use Serendipity\Test\Testing\Stub\Variety;
use stdClass;

final class FormatValueTest extends TestCase
{
    public function testFormatValueBuiltinSuccessfully(): void
    {
        $formatters = [
            'string' => new class implements Formatter {
                public function format(mixed $value, mixed $option = null): string
                {
                    return (string) $value;
                }
            },
            'int' => new class implements Formatter {
                public function format(mixed $value, mixed $option = null): int
                {
                    return (int) $value;
                }
            },
            'float' => fn () => new stdClass(),
        ];
        $chain = new FormatValue(formatters: $formatters, path: ['*']);
        $target = $chain->extractTarget(Builtin::class);
        $parameters = $target->parameters;

        $this->assertCount(6, $parameters);

        $set = Set::createFrom([
            'string' => 10,
            'int' => '10',
            'float' => ['val' => 10.1],
            'bool' => true,
        ]);

        [
            $string,
            $int,
            $float,
            $bool,
        ] = $parameters;

        $value = $chain->resolve($string, $set);
        $this->assertSame('10', $value->content);

        $value = $chain->resolve($int, $set);
        $this->assertSame(10, $value->content);

        $value = $chain->resolve($float, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals(
            "The value for '' must be of type 'float' and 'stdClass' was given.",
            $value->content->message
        );

        $value = $chain->resolve($bool, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals("The value given for '*' is not supported.", $value->content->message);
    }

    public function testTypeMatchedShouldResolveVariety(): void
    {
        $formatters = [
            'int|string' => fn (array $value) => array_shift($value),
            'Countable&Iterator' => fn () => new Intersected(),
            EntityStub::class => fn (array $value) => new EntityStub(...$value),
        ];
        $chain = new FormatValue(formatters: $formatters, path: ['*']);
        $target = $chain->extractTarget(Variety::class);
        $parameters = $target->parameters;

        $this->assertCount(4, $parameters);

        $set = Set::createFrom([
            'union' => [true, 'string', 10],
            'intersection' => null,
            'nested' => [
                10,
                10.1,
                'string',
                true,
                new NoConstructor(),
                null,
                null
            ],
            'whatever' => new stdClass(),
        ]);

        [
            $union,
            $intersection,
            $nested,
            $whatever,
        ] = $parameters;

        $value = $chain->resolve($union, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals(
            "The value for '*' must be of type 'int|string' and 'bool' was given.",
            $value->content->message
        );

        $value = $chain->resolve($intersection, $set);
        $this->assertInstanceOf(Intersected::class, $value->content);

        $value = $chain->resolve($nested, $set);
        $this->assertInstanceOf(EntityStub::class, $value->content);

        $value = $chain->resolve($whatever, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals("The value given for '*' is not supported.", $value->content->message);
    }
}
