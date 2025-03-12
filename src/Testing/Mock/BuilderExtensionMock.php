<?php

declare(strict_types=1);

namespace Serendipity\Testing\Mock;

use Closure;
use Serendipity\Infrastructure\Adapter\Serialize\Builder;
use Serendipity\Testing\Extension\BuilderExtension;

final class BuilderExtensionMock
{
    use BuilderExtension;

    public function __construct(
        private readonly Closure $assertion,
        private readonly Builder $mock,
    ) {
    }

    public function assert(): Builder
    {
        return $this->builder();
    }

    protected function make(string $class, array $args = []): Builder
    {
        call_user_func($this->assertion, Builder::class, $class);
        return $this->mock;
    }
}
