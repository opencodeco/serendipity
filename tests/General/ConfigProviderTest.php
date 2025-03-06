<?php

declare(strict_types=1);

namespace Serendipity\Test\General;

use PHPUnit\Framework\TestCase;
use Serendipity\ConfigProvider;
use Serendipity\Infrastructure\Database\Document\SleekDBFactory;
use Serendipity\Infrastructure\Database\Relational\ConnectionFactory;

/**
 * @internal
 */
class ConfigProviderTest extends TestCase
{
    public function testBeSuccessfully(): void
    {
        $provider = new ConfigProvider();
        $config = $provider();
        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey(SleekDBFactory::class, $config['dependencies']);
        $this->assertArrayHasKey(ConnectionFactory::class, $config['dependencies']);
        $this->assertArrayHasKey('annotations', $config);
    }
}
