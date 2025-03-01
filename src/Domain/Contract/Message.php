<?php

declare(strict_types=1);

namespace Serendipity\Domain\Contract;

use Serendipity\Domain\Support\Values;

interface Message
{
    public function properties(): Values;

    public function property(string $key, mixed $default = null): mixed;

    public function values(): ?Values;

    public function value(string $key, mixed $default = null): mixed;
}
