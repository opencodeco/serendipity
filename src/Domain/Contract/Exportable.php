<?php

declare(strict_types=1);

namespace Serendipity\Domain\Contract;

interface Exportable
{
    public function export(): mixed;
}
