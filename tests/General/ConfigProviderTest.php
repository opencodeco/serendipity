<?php

declare(strict_types=1);

namespace Serendipity\Test\General;

use PHPUnit\Framework\TestCase;
use Serendipity\ConfigProvider;

/**
 * @internal
 */
class ConfigProviderTest extends TestCase
{
    public function testBeSuccessfully(): void
    {
        $provider = new ConfigProvider();
        $config = $provider();
        $this->assertArrayHasKey('commands', $config);
        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey('annotations', $config);
    }
}
