<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Database\Relational;

use Hyperf\DB\DB as Database;
use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Database\Relational\HyperfConnectionFactory;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;

/**
 * @internal
 */
final class HyperfConnectionFactoryTest extends TestCase
{
    use MakeExtension;

    public function testShouldCreateDatabaseConnection(): void
    {
        $database = $this->make(Database::class);

        $factory = new HyperfConnectionFactory($database);
        $connection = $factory->make('default');

        $this->assertEquals('default', $connection->connection);
    }
}
