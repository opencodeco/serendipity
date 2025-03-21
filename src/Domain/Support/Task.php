<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support;

final class Task
{
    private string $correlationId = '';

    private string $platformId = '';

    public function setCorrelationId(string $correlationId): self
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    public function setPlatformId(string $platformId): self
    {
        $this->platformId = $platformId;
        return $this;
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function getPlatformId(): string
    {
        return $this->platformId;
    }
}
