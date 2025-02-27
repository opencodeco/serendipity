<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Persistence\Factory;

use Hyperf\Contract\ConfigInterface;
use Serendipity\Infrastructure\Persistence\Factory\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Testing\TestCase;

final class SleekDBDatabaseFactoryTest extends TestCase
{
    private ConfigInterface $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = $this->make(ConfigInterface::class);
    }

    public function testShouldCreateStore(): void
    {
        $config = $this->createMock(ConfigInterface::class);
        $config->expects($this->once())
            ->method('get')
            ->with('databases.sleek')
            ->willReturn($this->config->get('databases.sleek'));

        $factory = new SleekDBDatabaseFactory($config);
        $store = $factory->make('resource');
        $this->assertEquals('resource', $store->getStoreName());
    }
}
