<?php

declare(strict_types=1);

namespace Serendipity\Testing\Mock;

use Closure;
use Serendipity\Infrastructure\Database\Managed;
use Serendipity\Testing\Extension\ManagedExtension;

final class ManagedExtensionMock
{
    use ManagedExtension;

    public function __construct(
        private readonly Managed $managedMock,
        private readonly ?Closure $assertion = null,
    ) {
    }

    public function assertManaged(): Managed
    {
        return $this->managed();
    }

    protected function make(string $class, array $args = []): Managed
    {
        if ($this->assertion !== null) {
            call_user_func($this->assertion, $class, $args);
        }

        return $this->managedMock;
    }
}
