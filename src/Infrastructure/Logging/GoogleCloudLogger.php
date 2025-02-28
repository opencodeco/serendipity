<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Logging;

use Google\Cloud\Logging\PsrLogger;
use Psr\Log\LoggerInterface;
use Stringable;

class GoogleCloudLogger implements LoggerInterface
{
    public function __construct(private readonly PsrLogger $driver)
    {
    }

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->driver->emergency($message, $this->severity($context, 'emergency'));
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->driver->alert($message, $this->severity($context, 'alert'));
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->driver->critical($message, $this->severity($context, 'critical'));
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->driver->error($message, $this->severity($context, 'error'));
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->driver->warning($message, $this->severity($context, 'warning'));
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->driver->notice($message, $this->severity($context, 'notice'));
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->driver->info($message, $this->severity($context, 'info'));
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->driver->debug($message, $this->severity($context, 'debug'));
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->driver->log($level, $message, $this->severity($context, 'log'));
    }

    private function severity(array $context, string $severity): array
    {
        return array_merge(['severity' => $severity], $context);
    }
}
