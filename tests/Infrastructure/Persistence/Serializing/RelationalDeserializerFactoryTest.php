<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Persistence\Serializing;

use DateTime;
use DateTimeImmutable;
use Serendipity\Infrastructure\Persistence\Converter\FromArrayToDatabase;
use Serendipity\Infrastructure\Persistence\Converter\FromDatetimeToDatabase;
use Serendipity\Infrastructure\Persistence\Serializing\RelationalDeserializerFactory;
use Serendipity\Test\Infrastructure\Stub;
use Serendipity\Test\TestCase;

final class RelationalDeserializerFactoryTest extends TestCase
{
    public function testShouldCreateDeserializer(): void
    {
        $factory = new RelationalDeserializerFactory();
        $serializer = $factory->make(Stub::class);

        $this->assertEquals(Stub::class, $serializer->type);
        $converters = [
            'array' => new FromArrayToDatabase(),
            DateTime::class => new FromDatetimeToDatabase(),
            DateTimeImmutable::class => new FromDatetimeToDatabase(),
        ];
        $this->assertEquals($converters, $serializer->converters);
    }
}
