<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing\Observability\Logger\InMemory;

readonly class Record
{
    public function __construct(
        public string $level,
        public string $message,
        public array $context
    ) {
    }
}
