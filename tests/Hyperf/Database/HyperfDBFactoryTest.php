<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Database;

use Hyperf\DB\DB as Database;
use Serendipity\Hyperf\Database\HyperfDBFactory;
use Serendipity\Test\TestCase;

final class HyperfDBFactoryTest extends TestCase
{
    public function testShouldCreateDatabaseConnection(): void
    {
        $database = $this->make(Database::class);

        $factory = new HyperfDBFactory($database);
        $connection = $factory->make('default');

        $this->assertEquals('default', $connection->connection);
    }
}
