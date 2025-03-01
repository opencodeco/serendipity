<?php

declare(strict_types=1);

namespace Serendipity\Testing\Extension;

use Serendipity\Infrastructure\Adapter\Serialize\Builder;

/**
 * @phpstan-ignore trait.unused
 */
trait BuilderExtension
{
    private ?Builder $builder = null;

    protected function builder(): Builder
    {
        if ($this->builder === null) {
            $this->builder = $this->make(Builder::class);
        }
        return $this->builder;
    }

    abstract protected function make(string $class, array $args = []): mixed;
}
