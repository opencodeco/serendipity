<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Converter;

use DateTime;
use Serendipity\Infrastructure\Repository\Formatter\FromDatetimeToDatabase;
use Serendipity\Test\TestCase;

final class FromDatetimeToDatabaseTest extends TestCase
{
    final public function testConvertDatetimeToString(): void
    {
        $converter = new FromDatetimeToDatabase();
        $datetime = new DateTime('2023-01-01T00:00:00+00:00');
        $result = $converter->format($datetime);

        $this->assertIsString($result);
        $this->assertEquals('2023-01-01T00:00:00+00:00', $result);
    }

    final public function testConvertStringToString(): void
    {
        $converter = new FromDatetimeToDatabase();
        $string = '2023-01-01T00:00:00+00:00';
        $result = $converter->format($string);

        $this->assertIsString($result);
        $this->assertEquals($string, $result);
    }

    final public function testConvertInvalidTypeToNull(): void
    {
        $converter = new FromDatetimeToDatabase();
        $invalidValue = 123;
        $result = $converter->format($invalidValue);

        $this->assertNull($result);
    }
}
