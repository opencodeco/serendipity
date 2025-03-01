<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Formatter;

use Serendipity\Infrastructure\Repository\Formatter\FromArrayToDatabase;
use PHPUnit\Framework\TestCase;

final class FromArrayToDatabaseTest extends TestCase
{
    final public function testConvertArrayToString(): void
    {
        $converter = new FromArrayToDatabase();
        $array = ['key' => 'value'];
        $result = $converter->format($array);

        $this->assertIsString($result);
        $this->assertEquals('{"key":"value"}', $result);
    }

    final public function testConvertStringToString(): void
    {
        $converter = new FromArrayToDatabase();
        $string = '{"key":"value"}';
        $result = $converter->format($string);

        $this->assertIsString($result);
        $this->assertEquals($string, $result);
    }

    final public function testConvertInvalidTypeToNull(): void
    {
        $converter = new FromArrayToDatabase();
        $invalidValue = 123;
        $result = $converter->format($invalidValue);

        $this->assertNull($result);
    }
}
