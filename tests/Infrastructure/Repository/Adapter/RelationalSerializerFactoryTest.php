<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Adapter;

use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Repository\Adapter\RelationalSerializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\FromDatabaseToArray;
use Serendipity\Test\Infrastructure\Stub;

/**
 * @internal
 */
final class RelationalSerializerFactoryTest extends TestCase
{
    public function testShouldCreateSerializer(): void
    {
        $factory = new RelationalSerializerFactory();
        $serializer = $factory->make(Stub::class);

        $this->assertEquals(Stub::class, $serializer->type);
        $converters = ['array' => new FromDatabaseToArray()];
        $this->assertEquals($converters, $serializer->formatters);
    }
}
