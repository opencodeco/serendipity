<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Adapter;

use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Repository\Adapter\RelationalSerializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\RelationalJsonToArray;
use Serendipity\Test\Testing\Stub\Stub;

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
        $converters = ['array' => new RelationalJsonToArray()];
        $this->assertEquals($converters, $serializer->formatters);
    }
}
