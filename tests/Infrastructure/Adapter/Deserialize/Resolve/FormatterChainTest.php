<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize\Resolve;

use PHPUnit\Framework\Attributes\TestWith;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\ConverterChain;
use Serendipity\Test\TestCase;
use stdClass;

use function Serendipity\Type\Json\encode;

final class FormatterChainTest extends TestCase
{
    #[TestWith([10.5])]
    #[TestWith([true])]
    #[TestWith([null])]
    #[TestWith([new stdClass()])]
    public function testResolveWithoutConverter(mixed $value): void
    {
        $chain = new ConverterChain();
        $result = $chain->resolve($value);

        $this->assertSame($value, $result->content);
    }

    public function testResolveWithArrayValue(): void
    {
        $converter = new class implements Formatter {
            public function format(mixed $value, mixed $option = null): ?string
            {
                return encode($value);
            }
        };
        $chain = new ConverterChain(formatters: ['array' => $converter]);
        $value = ['key' => 'value'];
        $result = $chain->resolve($value);

        $this->assertEquals('{"key":"value"}', $result->content);
    }
}
