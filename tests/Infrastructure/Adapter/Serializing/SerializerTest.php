<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serializing;

use Serendipity\Infrastructure\Adapter\Serializing\Serializer;
use Serendipity\Infrastructure\Testing\TestCase;
use Serendipity\Test\Infrastructure\Stub;

class SerializerTest extends TestCase
{
    private Serializer $serializer;

    public function testSerialize(): void
    {
        $datum = ['foo' => 'John Doe', 'bar' => 30];

        $result = $this->serializer->serialize($datum);

        $this->assertEquals('John Doe', $result->foo);
        $this->assertEquals(30, $result->bar);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = new Serializer(Stub::class);
    }
}
