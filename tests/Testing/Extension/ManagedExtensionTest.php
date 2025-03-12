<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Extension;

use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Database\Managed;
use Serendipity\Testing\Mock\ManagedExtensionMock;

/**
 * @internal
 */
final class ManagedExtensionTest extends TestCase
{
    public function testManaged(): void
    {
        $mock = new ManagedExtensionMock(
            $this->createMock(Managed::class),
            fn (string $actual) => $this->assertEquals(Managed::class, $actual),
        );
        $mock->assertManaged();
    }
}
