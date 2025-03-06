<?php

declare(strict_types=1);

namespace Serendipity\Domain\Contract;

use Serendipity\Domain\Support\Set;

interface Message
{
    public function properties(): Set;

    public function content(): mixed;
}
