<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Formatter;

use MongoDB\Model\BSONArray;
use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Repository\Formatter\MongoArrayToEntity;

/**
 * @internal
 */
final class MongoArrayToEntityTest extends TestCase
{
    public function testConvertBSONArrayToArray(): void
    {
        $converter = new MongoArrayToEntity();
        $bsonArray = new BSONArray(['key' => 'value']);
        $result = $converter->format($bsonArray);

        $this->assertIsArray($result);
        $this->assertEquals(['key' => 'value'], $result);
    }

    public function testConvertArrayToArray(): void
    {
        $converter = new MongoArrayToEntity();
        $array = ['key' => 'value'];
        $result = $converter->format($array);

        $this->assertIsArray($result);
        $this->assertEquals($array, $result);
    }

    public function testConvertInvalidTypeToNull(): void
    {
        $converter = new MongoArrayToEntity();
        $invalidValue = 123;
        $result = $converter->format($invalidValue);

        $this->assertNull($result);
    }
}
