<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Database;

use Hyperf\DB\DB as Database;
use Serendipity\Hyperf\Database\HyperfDatabaseFactory;
use Serendipity\Test\TestCase;

final class HyperfDatabaseFactoryTest extends TestCase
{
    public function testShouldCreateDatabaseConnection(): void
    {
        $database = $this->make(Database::class);

        $factory = new HyperfDatabaseFactory($database);
        $connection = $factory->make('default');

        $this->assertEquals('default', $connection->connection);
    }
}
