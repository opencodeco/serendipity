<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support;

final class Task
{
    private string $resource = '';

    private string $correlationId = '';

    private string $invokerId = '';

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

    public function setInvokerId(string $invokerId): self
    {
        $this->invokerId = $invokerId;
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

    public function getInvokerId(): string
    {
        return $this->invokerId;
    }

    public function resume(): string
    {
        return sprintf('%s::%s::%s', $this->getResource(), $this->getCorrelationId(), $this->getInvokerId());
    }
}
