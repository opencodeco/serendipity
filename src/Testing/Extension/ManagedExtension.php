<?php

declare(strict_types=1);

namespace Serendipity\Testing\Extension;

use Serendipity\Infrastructure\Database\Managed;

trait ManagedExtension
{
    private ?Managed $managed = null;

    protected function managed(): Managed
    {
        if ($this->managed === null) {
            $this->managed = $this->make(class: Managed::class);
        }
        return $this->managed;
    }

    abstract protected function make(string $class, array $args = []): mixed;
}
