<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Formatter;

use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Repository\Formatter\RelationalArrayToJson;

/**
 * @internal
 */
final class RelationalArrayToJsonTest extends TestCase
{
    final public function testConvertArrayToString(): void
    {
        $converter = new RelationalArrayToJson();
        $array = ['key' => 'value'];
        $result = $converter->format($array);

        $this->assertIsString($result);
        $this->assertEquals('{"key":"value"}', $result);
    }

    final public function testConvertStringToString(): void
    {
        $converter = new RelationalArrayToJson();
        $string = '{"key":"value"}';
        $result = $converter->format($string);

        $this->assertIsString($result);
        $this->assertEquals($string, $result);
    }

    final public function testConvertInvalidTypeToNull(): void
    {
        $converter = new RelationalArrayToJson();
        $invalidValue = 123;
        $result = $converter->format($invalidValue);

        $this->assertNull($result);
    }
}
