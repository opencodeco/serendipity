<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Formatter;

use DateTime;
use DateTimeImmutable;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Repository\Formatter\MongoDateTimeToEntity;

/**
 * @internal
 */
final class MongoDateTimeToEntityTest extends TestCase
{
    public function testConvertUTCDateTimeToDateTimeImmutable(): void
    {
        $converter = new MongoDateTimeToEntity();
        $utcDateTime = new UTCDateTime((new DateTime())->getTimestamp() * 1000);
        $result = $converter->format($utcDateTime, DateTimeImmutable::class);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
    }

    public function testConvertStringToDateTimeImmutable(): void
    {
        $converter = new MongoDateTimeToEntity();
        $string = '2023-01-01T00:00:00+00:00';
        $result = $converter->format($string);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
    }

    public function testConvertInvalidTypeToNull(): void
    {
        $converter = new MongoDateTimeToEntity();
        $invalidValue = 123;
        $result = $converter->format($invalidValue);

        $this->assertNull($result);
    }
}
