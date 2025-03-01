<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter;

use Serendipity\Infrastructure\Adapter\SerializerFactory;
use Serendipity\Test\Infrastructure\Stub;
use Serendipity\Test\TestCase;

final class SerializerFactoryTest extends TestCase
{
    public function testShouldCreateSerializer(): void
    {
        $factory = new SerializerFactory();
        $serializer = $factory->make(Stub::class);

        $this->assertEquals(Stub::class, $serializer->type);
        $this->assertEquals([], $serializer->formatters);
    }
}
