<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support;

final class Task
{
    private string $resource = '';

    private string $correlationId = '';

    private string $platformId = '';

    public function setResource(string $resource): self
    {
        $this->resource = $resource;
        return $this;
    }

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

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function getPlatformId(): string
    {
        return $this->platformId;
    }

    public function resume(): string
    {
        return sprintf('%s::%s::%s', $this->getResource(), $this->getCorrelationId(), $this->getPlatformId());
    }
}
