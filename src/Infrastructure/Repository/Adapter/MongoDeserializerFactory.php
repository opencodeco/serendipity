<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Adapter;

use DateTime;
use DateTimeImmutable;
use Serendipity\Infrastructure\Adapter\DeserializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\MongoDateTimeToDatabase;

class MongoDeserializerFactory extends DeserializerFactory
{
    protected function formatters(): array
    {
        return [
            DateTime::class => new MongoDateTimeToDatabase(),
            DateTimeImmutable::class => new MongoDateTimeToDatabase(),
        ];
    }
}
