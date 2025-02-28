<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Logging;

use Google\Cloud\Logging\LoggingClient;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LoggerInterface;

use function Serendipity\Type\Cast\toString;

readonly class EnvironmentLoggerFactory
{
    public function __construct(
        private StdoutLoggerInterface $stdoutLogger,
        private ConfigInterface $config,
    ) {
    }

    public function make(string $env = 'dev'): LoggerInterface
    {
        if ($env === 'dev') {
            return $this->stdoutLogger;
        }

        $projectId = toString($this->config->get('logger.gcloud.project_id', 'unknown'));
        $config = [
            'projectId' => $projectId,
        ];
        $logging = new LoggingClient($config);
        $options = [];
        if ($this->config->get('logger.gcloud.batch', false)) {
            $options = [
                'batchEnabled' => true,
                'batchOptions' => [
                    'batchSize' => 50,
                    'callPeriod' => 5,
                ],
            ];
        }
        $driver = $logging->logger('google-cloud', $options);
        $serviceName = toString($this->config->get('logger.gcloud.service_name', 'unknown'));
        return new GoogleCloudLogger($driver, $projectId, $serviceName, $env);
    }
}
