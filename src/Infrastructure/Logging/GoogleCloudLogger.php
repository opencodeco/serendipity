<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Logging;

use DateTimeImmutable;
use DateTimeInterface;
use Google\Cloud\Logging\Entry;
use Google\Cloud\Logging\Logger;
use Psr\Log\LoggerInterface;
use Stringable;
use Throwable;

use function Serendipity\Type\Json\encode;

class GoogleCloudLogger implements LoggerInterface
{
    public function __construct(
        private readonly Logger $driver,
        public readonly string $projectId,
        public readonly string $serviceName,
        public readonly string $env,
        public readonly string $type = 'cloud_run_revision',
        public readonly string $location = 'us-central1',
    ) {
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $levels = [
            Logger::EMERGENCY => 'EMERGENCY',
            Logger::ALERT => 'ALERT',
            Logger::CRITICAL => 'CRITICAL',
            Logger::ERROR => 'ERROR',
            Logger::WARNING => 'WARNING',
            Logger::NOTICE => 'NOTICE',
            Logger::INFO => 'INFO',
            Logger::DEBUG => 'DEBUG',
        ];
        $context['message'] = $message;
        $severity = $levels[$level] ?? $levels[Logger::INFO];
        $payload = $this->payload($severity, $context);
        $this->write($severity, $message, $payload);
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(Logger::EMERGENCY, $message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(Logger::INFO, $message, $context);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(Logger::ALERT, $message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(Logger::CRITICAL, $message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(Logger::ERROR, $message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(Logger::WARNING, $message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(Logger::NOTICE, $message, $context);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(Logger::DEBUG, $message, $context);
    }

    private function write(string $severity, string|Stringable $message, array $context): void
    {
        try {
            $this->driver->write(new Entry($context));
        } catch (Throwable $error) {
            $detail = sprintf('"%s" in `%s` at `%s`', $error->getMessage(), $error->getFile(), $error->getLine());
            $stdout = sprintf('[GoogleCloudLogger][%s] %s: %s (%s)', $severity, $message, encode($context), $detail);
            printf("%s\n", $stdout);
        }
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
}
