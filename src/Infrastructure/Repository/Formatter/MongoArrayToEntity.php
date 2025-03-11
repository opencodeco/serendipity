<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Formatter;

use MongoDB\Model\BSONArray;
use Serendipity\Domain\Contract\Formatter;

class MongoArrayToEntity implements Formatter
{
    public function format(mixed $value, mixed $option = null): ?array
    {
        return match (true) {
            $value instanceof BSONArray => $value->getArrayCopy(),
            is_array($value) => $value,
            default => null
        };
    }
}
