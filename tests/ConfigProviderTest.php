<?php

declare(strict_types=1);

namespace Serendipity\Test;

use PHPUnit\Framework\TestCase;
use Serendipity\ConfigProvider;
use Serendipity\Infrastructure\Database\Document\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Database\Relational\RelationalDatabaseFactory;

class ConfigProviderTest extends TestCase
{
    public function testBeSuccessfully(): void
    {
        $provider = new ConfigProvider();
        $config = $provider();
        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey(SleekDBDatabaseFactory::class, $config['dependencies']);
        $this->assertArrayHasKey(RelationalDatabaseFactory::class, $config['dependencies']);
        $this->assertArrayHasKey('annotations', $config);
    }
}
