<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter;

use Serendipity\Infrastructure\Adapter\DeserializerFactory;
use Serendipity\Test\Infrastructure\Stub;
use PHPUnit\Framework\TestCase;

final class DeserializerFactoryTest extends TestCase
{
    public function testShouldCreateDeserializer(): void
    {
        $factory = new DeserializerFactory();
        $deserializer = $factory->make(Stub::class);

        $this->assertEquals(Stub::class, $deserializer->type);
        $this->assertEquals([], $deserializer->formatters);
    }
}
