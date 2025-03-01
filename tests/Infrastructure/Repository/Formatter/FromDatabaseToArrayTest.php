<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Formatter;

use Serendipity\Infrastructure\Repository\Formatter\FromDatabaseToArray;
use Serendipity\Test\TestCase;

final class FromDatabaseToArrayTest extends TestCase
{
    final public function testConvertStringToArray(): void
    {
        $converter = new FromDatabaseToArray();
        $string = '{"key":"value"}';
        $result = $converter->format($string);

        $this->assertIsArray($result);
        $this->assertEquals(['key' => 'value'], $result);
    }

    final public function testConvertArrayToArray(): void
    {
        $converter = new FromDatabaseToArray();
        $array = ['key' => 'value'];
        $result = $converter->format($array);

        $this->assertIsArray($result);
        $this->assertEquals($array, $result);
    }

    final public function testConvertInvalidTypeToNull(): void
    {
        $converter = new FromDatabaseToArray();
        $invalidValue = 123;
        $result = $converter->format($invalidValue);

        $this->assertNull($result);
    }
}
