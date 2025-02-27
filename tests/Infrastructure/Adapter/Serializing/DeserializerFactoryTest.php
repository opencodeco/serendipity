<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serializing;

use Serendipity\Infrastructure\Adapter\Serializing\DeserializerFactory;
use Serendipity\Infrastructure\Testing\TestCase;
use Serendipity\Test\Infrastructure\Stub;

final class DeserializerFactoryTest extends TestCase
{
    public function testShouldCreateDeserializer(): void
    {
        $factory = new DeserializerFactory();
        $deserializer = $factory->make(Stub::class);

        $this->assertEquals(Stub::class, $deserializer->type);
        $this->assertEquals([], $deserializer->converters);
    }
}
