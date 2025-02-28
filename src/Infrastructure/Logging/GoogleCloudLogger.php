<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Logging;

use Google\Cloud\Logging\Logger;
use Psr\Log\LoggerInterface;
use Stringable;

class GoogleCloudLogger implements LoggerInterface
{
    public function __construct(private readonly Logger $driver)
    {
    }

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $options = [
            'severity' => Logger::EMERGENCY,
            'context' => $context,
        ];
        $entry = $this->driver->entry($message, $options);
        $this->driver->write($entry);
        printf(sprintf("[%s] '%s" . PHP_EOL, $level, $message));
    }
}
