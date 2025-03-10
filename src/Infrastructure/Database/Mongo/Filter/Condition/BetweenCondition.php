<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Database\Mongo\Filter\Condition;

use DateTime;
use InvalidArgumentException;
use MongoDB\BSON\UTCDateTime;
use Serendipity\Infrastructure\Database\Mongo\Filter\Condition;
use Throwable;

class BetweenCondition implements Condition
{
    public function compose(string $value): array
    {
        $multiple = explode(',', $value, 2);
        if (count($multiple) < 2) {
            throw new InvalidArgumentException(sprintf("Invalid 'between' value '%s'", $value));
        }
        return [
            '$gte' => $this->convertToDate($multiple[0]),
            '$lte' => $this->convertToDate($multiple[1], true),
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function convertToDate(string $value, bool $pad = false): UTCDateTime
    {
        try {
            $dateTime = new DateTime($value);

            if ($pad && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                $dateTime->setTime(23, 59, 59);
            }

            return new UTCDateTime($dateTime->getTimestamp() * 1000);
        } catch (Throwable $previous) {
            throw new InvalidArgumentException(
                message: sprintf('Invalid date format: %s', $value),
                previous: $previous
            );
        }
    }
}
