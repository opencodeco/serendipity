<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Persistence;

use Serendipity\Infrastructure\Persistence\Factory\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Persistence\Generator;
use Serendipity\Test\TestCase;

final class SleekDBRepositoryTest extends TestCase
{
    public function testResource(): void
    {
        $generator = $this->createMock(Generator::class);
        $databaseFactory = $this->createMock(SleekDBDatabaseFactory::class);
        $databaseFactory->expects($this->once())->method('make')->with('x');
        new SleekDBRepositoryTestMock($generator, $databaseFactory);
    }
}
