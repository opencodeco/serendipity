<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Logging;

use Hyperf\Contract\ConfigInterface;
use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Task;
use Serendipity\Hyperf\Logging\GoogleCloudLoggerFactory;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;

final class GoogleCloudLoggerFactoryTest extends TestCase
{
    use MakeExtension;

    public function testShouldMakeGoogleCloudLogger(): void
    {
        $task = $this->make(Task::class);
        $config = $this->createMock(ConfigInterface::class);
        $config->expects($this->exactly(4))
            ->method('get')
            ->willReturnCallback(fn (string $key) => match ($key) {
                'logger.gcloud.project_id' => 'project-id',
                'logger.gcloud.options' => [],
                'logger.gcloud.format' => '{{message}}',
                'logger.gcloud.service_name' => 'service-name',
                default => null,
            });

        $factory = new GoogleCloudLoggerFactory($task, $config);

        $logger = $factory->make('prd');

        $this->assertEquals('prd', $logger->env);
    }
}
