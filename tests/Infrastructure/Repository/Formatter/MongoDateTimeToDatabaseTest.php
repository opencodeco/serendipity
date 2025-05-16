<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Formatter;

use DateTime;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Repository\Formatter\MongoDateTimeToDatabase;

final class MongoDateTimeToDatabaseTest extends TestCase
{
    public function testConvertDateTimeToUTCDateTime(): void
    {
        $converter = new MongoDateTimeToDatabase();
        $datetime = new DateTime('2023-01-01T00:00:00+00:00', new DateTimeZone('UTC'));
        $result = $converter->format($datetime);

        $this->assertInstanceOf(UTCDateTime::class, $result);
    }

    public function testConvertStringToUTCDateTime(): void
    {
        $converter = new MongoDateTimeToDatabase();
        $string = '2023-01-01T00:00:00+00:00';
        $result = $converter->format($string);

        $this->assertInstanceOf(UTCDateTime::class, $result);
    }

    public function testConvertInvalidTypeToNull(): void
    {
        $converter = new MongoDateTimeToDatabase();
        $invalidValue = 123;
        $result = $converter->format($invalidValue);

        $this->assertNull($result);
    }
}
