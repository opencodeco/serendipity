<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Adapter;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Repository\Adapter\MongoDeserializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\MongoDateTimeToDatabase;
use Serendipity\Test\Testing\Stub\Stub;

/**
 * @internal
 */
final class MongoDeserializerFactoryTest extends TestCase
{
    public function testShouldCreateDeserializer(): void
    {
        $factory = new MongoDeserializerFactory();
        $deserializer = $factory->make(Stub::class);

        $converters = [
            DateTime::class => new MongoDateTimeToDatabase(),
            DateTimeImmutable::class => new MongoDateTimeToDatabase(),
        ];
        $this->assertEquals($converters, $deserializer->formatters);
    }
}
