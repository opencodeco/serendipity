<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Database;

use Hyperf\DB\DB as Database;
use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Database\HyperfDatabaseFactory;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;

/**
 * @internal
 */
final class HyperfDatabaseFactoryTest extends TestCase
{
    use MakeExtension;

    public function testShouldCreateDatabaseConnection(): void
    {
        $database = $this->make(Database::class);

        $factory = new HyperfDatabaseFactory($database);
        $connection = $factory->make('default');

        $this->assertEquals('default', $connection->connection);
    }
}
