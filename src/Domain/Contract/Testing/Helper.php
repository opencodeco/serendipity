<?php

declare(strict_types=1);

namespace Serendipity\Domain\Contract\Testing;

use Serendipity\Domain\Support\Set;

interface Helper
{
    public function truncate(string $resource): void;

    public function seed(string $type, string $resource, array $override = []): Set;

    public function count(string $resource, array $filters): int;
}
