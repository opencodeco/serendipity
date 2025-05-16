<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Extension;

use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Adapter\Serialize\Builder;
use Serendipity\Testing\Mock\BuilderExtensionMock;

final class BuilderExtensionTest extends TestCase
{
    public function testBuilder(): void
    {
        $mock = new BuilderExtensionMock(
            fn (mixed $actual) => $this->assertEquals(Builder::class, $actual),
            $this->createMock(Builder::class),
        );
        $mock->assert();
    }
}
