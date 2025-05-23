<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Adapter;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Collection\Collection;
use Serendipity\Infrastructure\Repository\Adapter\RelationalSerializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\RelationalJsonToArray;
use Serendipity\Test\Testing\Stub\Stub;

final class RelationalSerializerFactoryTest extends TestCase
{
    public function testShouldCreateSerializer(): void
    {
        $factory = new RelationalSerializerFactory();
        $serializer = $factory->make(Stub::class);

        $this->assertEquals(Stub::class, $serializer->type);
        $converters = [
            Collection::class => new RelationalJsonToArray(),
            'array' => new RelationalJsonToArray(),
        ];
        $this->assertEquals($converters, $serializer->formatters);
    }
}
