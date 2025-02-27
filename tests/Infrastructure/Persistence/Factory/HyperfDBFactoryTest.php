<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Persistence\Factory;

use Hyperf\DB\DB as Database;
use Serendipity\Infrastructure\Persistence\Factory\HyperfDBFactory;
use Serendipity\Infrastructure\Testing\TestCase;

final class HyperfDBFactoryTest extends TestCase
{
    public function testShouldCreateDatabaseConnection(): void
    {
        $database = $this->make(Database::class);

        $factory = new HyperfDBFactory($database);
        $connection = $factory->make('default');

        $reflection = new \ReflectionClass($connection);
        $property = $reflection->getProperty('poolName');
        $property->setAccessible(true);
        $poolName = $property->getValue($connection);

        $this->assertEquals('default', $poolName);
    }
}
