<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Logging;

use Google\Cloud\Logging\Logger as GoogleCloud;
use Psr\Log\LoggerInterface;
use Stringable;

abstract class Logger implements LoggerInterface
{
    final public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(GoogleCloud::EMERGENCY, $message, $context);
    }

    final public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(GoogleCloud::INFO, $message, $context);
    }

    final public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(GoogleCloud::ALERT, $message, $context);
    }

    final public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(GoogleCloud::CRITICAL, $message, $context);
    }

    final public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(GoogleCloud::ERROR, $message, $context);
    }

    final public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(GoogleCloud::WARNING, $message, $context);
    }

    final public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(GoogleCloud::NOTICE, $message, $context);
    }

    final public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(GoogleCloud::DEBUG, $message, $context);
    }
}
