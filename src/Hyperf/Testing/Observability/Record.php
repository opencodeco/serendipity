<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing\Observability;

readonly class Record
{
    public function __construct(
        public string $level,
        public string $message,
        public array $context
    ) {
    }
}
