<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Formatter;

use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Repository\Formatter\RelationalJsonToArray;

final class RelationalJsonToArrayTest extends TestCase
{
    public function testConvertStringToArray(): void
    {
        $converter = new RelationalJsonToArray();
        $string = '{"key":"value"}';
        $result = $converter->format($string);

        $this->assertIsArray($result);
        $this->assertEquals(['key' => 'value'], $result);
    }

    public function testConvertArrayToArray(): void
    {
        $converter = new RelationalJsonToArray();
        $array = ['key' => 'value'];
        $result = $converter->format($array);

        $this->assertIsArray($result);
        $this->assertEquals($array, $result);
    }

    public function testConvertInvalidTypeToNull(): void
    {
        $converter = new RelationalJsonToArray();
        $invalidValue = 123;
        $result = $converter->format($invalidValue);

        $this->assertNull($result);
    }
}
