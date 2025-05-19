<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Formatter;

use DateMalformedStringException;
use DateTimeInterface;
use MongoDB\BSON\UTCDateTime;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Domain\Type\Timestamp;

class MongoTimestampToEntity implements Formatter
{
    /**
     * @throws DateMalformedStringException
     */
    public function format(mixed $value, mixed $option = null): ?Timestamp
    {
        return match (true) {
            $value instanceof Timestamp => $value,
            $value instanceof UTCDateTime => new Timestamp(
                $value->toDateTime()->format(DateTimeInterface::ATOM)
            ),
            is_string($value) => new Timestamp($value),
            default => null
        };
    }
}
