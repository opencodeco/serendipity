<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Database\Document\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Database\Instrument;

/**
 * @internal
 */
final class SleekDBRepositoryTest extends TestCase
{
    public function testResource(): void
    {
        $generator = $this->createMock(Instrument::class);
        $databaseFactory = $this->createMock(SleekDBDatabaseFactory::class);
        $databaseFactory->expects($this->once())->method('make')->with('x');
        new SleekDBRepositoryTestMock($generator, $databaseFactory);
    }
}
