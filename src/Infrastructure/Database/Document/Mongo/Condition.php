<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Database\Document\Mongo;

interface Condition
{
    public function compose(string $value): array;
}
