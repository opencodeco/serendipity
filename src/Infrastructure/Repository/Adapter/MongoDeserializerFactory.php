<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Adapter;

use DateTime;
use DateTimeImmutable;
use Serendipity\Domain\Type\Timestamp;
use Serendipity\Infrastructure\Adapter\DeserializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\MongoDateTimeToDatabase;
use Serendipity\Infrastructure\Repository\Formatter\MongoTimestampToDatabase;

class MongoDeserializerFactory extends DeserializerFactory
{
    protected function formatters(): array
    {
        return [
            Timestamp::class => new MongoTimestampToDatabase(),
            DateTime::class => new MongoDateTimeToDatabase(),
            DateTimeImmutable::class => new MongoDateTimeToDatabase(),
        ];
    }
}
