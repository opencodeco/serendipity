<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository;

use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use Serendipity\Infrastructure\Database\Document\MongoFactory;
use Serendipity\Infrastructure\Database\Managed;

abstract class MongoRepository extends Repository
{
    protected readonly Collection $collection;

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
}
