<?php

declare(strict_types=1);

namespace Serendipity\Testing\Extension;

use Serendipity\Infrastructure\Database\Instrumental;

trait InstrumentalExtension
{
    private ?Instrumental $instrument = null;

    protected function instrumental(): Instrumental
    {
        if ($this->instrument === null) {
            $this->instrument = $this->make(Instrumental::class);
        }
        return $this->instrument;
    }

    abstract protected function make(string $class, array $args = []): mixed;
}
