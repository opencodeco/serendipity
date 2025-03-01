<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Database;

use Hyperf\DB\DB as Database;
use Serendipity\Hyperf\Database\HyperfDatabaseFactory;
use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Testing\CanMake;

final class HyperfDatabaseFactoryTest extends TestCase
{
    use CanMake;

    public function testShouldCreateDatabaseConnection(): void
    {
        $database = $this->make(Database::class);

        $factory = new HyperfDatabaseFactory($database);
        $connection = $factory->make('default');

        $this->assertEquals('default', $connection->connection);
    }
}
