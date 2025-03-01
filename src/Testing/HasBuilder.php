<?php

declare(strict_types=1);

namespace Serendipity\Testing;

use Serendipity\Infrastructure\Adapter\Serialize\Builder;

trait HasBuilder
{
    protected ?Builder $builder = null;

    protected function builder(): Builder
    {
        if ($this->builder === null) {
            $this->builder = $this->make(Builder::class);
        }
        return $this->builder;
    }

    abstract protected function make(string $class, array $args = []): mixed;
}
