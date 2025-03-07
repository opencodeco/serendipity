<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Logging;

use Hyperf\Contract\ConfigInterface;
use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Logging\GoogleCloudLoggerFactory;

/**
 * @internal
 */
final class GoogleCloudLoggerFactoryTest extends TestCase
{
    public function testShouldMakeGoogleCloudLogger(): void
    {
        $config = $this->createMock(ConfigInterface::class);
        $config->expects($this->exactly(3))
            ->method('get')
            ->willReturnCallback(fn (string $key) => match ($key) {
                'logger.gcloud.project_id' => 'project-id',
                'logger.gcloud.options' => [],
                'logger.gcloud.service_name' => 'service-name',
                default => null,
            });

        $factory = new GoogleCloudLoggerFactory($config);

        $logger = $factory->make('prd');

        $this->assertEquals('prd', $logger->env);
    }
}
