<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Logging;

use DateTimeImmutable;
use DateTimeInterface;
use Google\Cloud\Logging\Entry;
use Google\Cloud\Logging\Logger as GoogleCloud;
use Stringable;
use Throwable;

use function Serendipity\Coroutine\coroutine;
use function Serendipity\Type\Json\encode;

class GoogleCloudLogger extends Logger
{
    public function __construct(
        private readonly GoogleCloud $driver,
        public readonly string $projectId,
        public readonly string $serviceName,
        public readonly string $env,
        public readonly string $type = 'cloud_run_revision',
        public readonly string $location = 'us-central1',
        public readonly bool $useCoroutine = true,
    ) {
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $levels = [
            GoogleCloud::EMERGENCY => 'EMERGENCY',
            GoogleCloud::ALERT => 'ALERT',
            GoogleCloud::CRITICAL => 'CRITICAL',
            GoogleCloud::ERROR => 'ERROR',
            GoogleCloud::WARNING => 'WARNING',
            GoogleCloud::NOTICE => 'NOTICE',
            GoogleCloud::INFO => 'INFO',
            GoogleCloud::DEBUG => 'DEBUG',
        ];
        $context['message'] = $message;
        $severity = $levels[$level] ?? $levels[GoogleCloud::INFO];
        $payload = $this->payload($severity, $context);
        $write = fn () => $this->write($severity, $message, $payload);

        $this->useCoroutine
            ? coroutine($write)
            : $write();
    }

    private function payload(string $severity, array $context): array
    {
        return [
            'timestamp' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
            'logName' => $this->logName(),
            'severity' => $severity,
            'jsonPayload' => $context,
            'resource' => [
                'labels' => [
                    'configuration_name' => $this->serviceName,
                    'location' => $this->location,
                    'service_name' => $this->serviceName,
                    'project_id' => $this->projectId,
                ],
                'type' => $this->type,
            ],
        ];
    }

    private function logName(): string
    {
        return sprintf('projects/%s/logs/%s%%2F%s-%s', $this->serviceName, $this->projectId, 'env', $this->env);
    }

    private function write(string $severity, string|Stringable $message, array $context): void
    {
        try {
            $this->driver->write(new Entry($context));
        } catch (Throwable $error) {
            $detail = sprintf('"%s" in `%s` at `%s`', $error->getMessage(), $error->getFile(), $error->getLine());
            $stdout = sprintf(
                '[GoogleCloudLogger][%s] %s: %s (%s)',
                $severity,
                $message,
                encode($context),
                $detail
            );
            printf("%s\n", $stdout);
        }
    }
}
