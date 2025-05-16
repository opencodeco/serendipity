<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Formatter;

use DateTime;
use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Repository\Formatter\RelationalDatetimeToString;

final class RelationalDatetimeToStringTest extends TestCase
{
    final public function testConvertDatetimeToString(): void
    {
        $converter = new RelationalDatetimeToString();
        $datetime = new DateTime('2023-01-01T00:00:00+00:00');
        $result = $converter->format($datetime);

        $this->assertIsString($result);
        $this->assertEquals('2023-01-01T00:00:00+00:00', $result);
    }

    final public function testConvertStringToString(): void
    {
        $converter = new RelationalDatetimeToString();
        $string = '2023-01-01T00:00:00+00:00';
        $result = $converter->format($string);

        $this->assertIsString($result);
        $this->assertEquals($string, $result);
    }

    final public function testConvertInvalidTypeToNull(): void
    {
        $converter = new RelationalDatetimeToString();
        $invalidValue = 123;
        $result = $converter->format($invalidValue);

        $this->assertNull($result);
    }
}
