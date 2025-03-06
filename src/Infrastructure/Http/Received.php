<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Http;

use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Set;

readonly class Received implements Message
{
    public function __construct(
        private array $headers,
        private ?string $content = null
    ) {
    }

    public function properties(): Set
    {
        return Set::createFrom($this->headers);
    }

    public function content(): ?string
    {
        return $this->content;
    }
}
