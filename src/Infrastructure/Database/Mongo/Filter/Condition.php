<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Database\Mongo\Filter;

interface Condition
{
    public function compose(string $value): array;
}
