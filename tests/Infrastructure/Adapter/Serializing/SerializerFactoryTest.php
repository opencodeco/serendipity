<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serializing;

use Serendipity\Infrastructure\Adapter\Serializing\SerializerFactory;
use Serendipity\Infrastructure\Testing\TestCase;
use Serendipity\Test\Infrastructure\Stub;

final class SerializerFactoryTest extends TestCase
{
    public function testShouldCreateSerializer(): void
    {
        $factory = new SerializerFactory();
        $serializer = $factory->make(Stub::class);

        $this->assertEquals(Stub::class, $serializer->type);
        $this->assertEquals([], $serializer->converters);
    }
}
