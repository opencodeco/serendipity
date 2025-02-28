<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Logging;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Serendipity\Infrastructure\Logging\EnvironmentLoggerFactory;
use Serendipity\Test\TestCase;

class EnvironmentLoggerFactoryTest extends TestCase
{
    private StdoutLoggerInterface $stdoutLogger;

    private ConfigInterface $config;

    private EnvironmentLoggerFactory $factory;

    public function testShouldReturnStdoutLoggerForDevEnv(): void
    {
        $logger = $this->factory->make();

        $this->assertSame($this->stdoutLogger, $logger);
    }

    public function testShouldReturnBatchGcloudLoggerWhenConfigured(): void
    {
        $this->config->expects($this->exactly(3))
            ->method('get')
            ->willReturnCallback(fn (string $key) => match ($key) {
                'logger.gcloud.project_id' => 'project-id',
                'logger.gcloud.batch' => true,
                'logger.gcloud.service_name' => 'service-name',
                default => null,
            });

        $logger = $this->factory->make('prd');

        $this->assertNotSame($this->stdoutLogger, $logger);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->stdoutLogger = $this->createMock(StdoutLoggerInterface::class);
        $this->config = $this->createMock(ConfigInterface::class);
        $this->factory = new EnvironmentLoggerFactory($this->stdoutLogger, $this->config);
    }
}
