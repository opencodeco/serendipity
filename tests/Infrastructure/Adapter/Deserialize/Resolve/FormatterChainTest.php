<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize\Resolve;

use PHPUnit\Framework\Attributes\TestWith;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\ConverterChain;
use Serendipity\Test\TestCase;
use stdClass;

use function Serendipity\Type\Json\encode;

final class ConverterChainTest extends TestCase
{
    #[TestWith(['string'])]
    #[TestWith([10])]
    #[TestWith([10.5])]
    #[TestWith([true])]
    #[TestWith([null])]
    #[TestWith([new stdClass()])]
    final public function testResolveWithoutConverter(mixed $value): void
    {
        $chain = new ConverterChain();
        $result = $chain->resolve($value);

        $this->assertSame($value, $result->content);
    }

    final public function testResolveWithArrayValue(): void
    {
        $converter = new class implements Formatter {
            public function format(mixed $value, array $options = []): ?string
            {
                return encode($value);
            }
        };
        $chain = new ConverterChain(converters: ['array' => $converter]);
        $value = ['key' => 'value'];
        $result = $chain->resolve($value);

        $this->assertEquals('{"key":"value"}', $result->content);
    }
}
