<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository\Factory;

use Hyperf\DB\DB as Database;
use ReflectionClass;
use Serendipity\Infrastructure\Repository\Factory\HyperfDBFactory;
use Serendipity\Test\TestCase;

final class HyperfDBFactoryTest extends TestCase
{
    public function testShouldCreateDatabaseConnection(): void
    {
        $database = $this->make(Database::class);

        $factory = new HyperfDBFactory($database);
        $connection = $factory->make('default');

        $reflection = new ReflectionClass($connection);
        $property = $reflection->getProperty('poolName');
        $property->setAccessible(true);
        $poolName = $property->getValue($connection);

        $this->assertEquals('default', $poolName);
    }
}
