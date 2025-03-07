<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing\Observability;

readonly class LogRecord
{
    public function __construct(
        public string $level,
        public string $message,
        public array $context
    ) {
    }
}
