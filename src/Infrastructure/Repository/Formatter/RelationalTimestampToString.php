<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Formatter;

use Serendipity\Domain\Contract\Formatter;
use Serendipity\Domain\Type\Timestamp;

use function is_string;

class RelationalTimestampToString implements Formatter
{
    public function format(mixed $value, mixed $option = null): ?string
    {
        return match (true) {
            is_string($value) => $value,
            $value instanceof Timestamp => $value->toString(),
            default => null,
        };
    }
}
