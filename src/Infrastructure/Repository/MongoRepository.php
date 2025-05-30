<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository;

use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use Serendipity\Infrastructure\Database\Document\MongoFactory;
use Serendipity\Infrastructure\Database\Managed;

abstract class MongoRepository extends Repository
{
    protected readonly Collection $collection;

    protected bool $transform = true;

    public function __construct(
        protected readonly Managed $managed,
        MongoFactory $mongoFactory,
    ) {
        $this->collection = $mongoFactory->make($this->resource());
    }

    abstract protected function resource(): string;

    /**
     * @throws DateMalformedStringException
     */
    protected function toDateTime(string $datetime): UTCDateTime
    {
        $dateTime = new DateTime($datetime, new DateTimeZone('UTC'));
        return new UTCDateTime($dateTime->getTimestamp() * 1000);
    }

    protected function toArray(mixed $datum): array
    {
        return $this->transform
            ? $this->transform($datum)
            : parent::toArray($datum);
    }

    protected function transform(mixed $datum): array
    {
        if ($datum instanceof BSONDocument || $datum instanceof BSONArray) {
            $datum = $datum->getArrayCopy();
        }
        if (is_array($datum)) {
            $this->transformRecursive($datum);
            return $datum;
        }
        return parent::toArray($datum);
    }

    private function transformRecursive(array &$array): void
    {
        foreach ($array as &$value) {
            if ($value instanceof BSONDocument || $value instanceof BSONArray) {
                $value = $value->getArrayCopy();
                $this->transformRecursive($value);
                continue;
            }
            if (is_array($value)) {
                $this->transformRecursive($value);
            }
        }
        unset($value);
    }
}
