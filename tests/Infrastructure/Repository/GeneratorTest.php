<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Infrastructure\Database\Instrumental;

/**
 * @internal
 */
final class GeneratorTest extends TestCase
{
    final public function testId(): void
    {
        $generator = new Instrumental();
        $id = $generator->id();
        $this->assertIsString($id);
        $this->assertGreaterThanOrEqual(4, strlen($id));
        $this->assertLessThanOrEqual(32, strlen($id));
    }

    final public function testNow(): void
    {
        $generator = new Instrumental();
        $now = $generator->now();
        $this->assertIsString($now);
    }

    final public function testIdWithLength(): void
    {
        $this->expectException(GeneratingException::class);
        $this->expectExceptionMessage('Error generating "id": "maxLength: cannot be less than 4 or greater than 32."');
        $generator = new Instrumental(0);
        $generator->id();
    }
}
