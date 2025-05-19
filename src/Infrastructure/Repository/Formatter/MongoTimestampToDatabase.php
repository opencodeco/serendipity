<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Formatter;

use DateMalformedStringException;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Domain\Type\Timestamp;

class MongoTimestampToDatabase implements Formatter
{
    /**
     * @throws DateMalformedStringException
     */
    public function format(mixed $value, mixed $option = null): ?UTCDateTime
    {
        if ($value instanceof Timestamp) {
            return new UTCDateTime($value->getTimestamp() * 1000);
        }
        if (is_string($value)) {
            $dateTime = new Timestamp($value, new DateTimeZone('UTC'));
            return new UTCDateTime($dateTime->getTimestamp() * 1000);
        }
        return null;
    }
}
