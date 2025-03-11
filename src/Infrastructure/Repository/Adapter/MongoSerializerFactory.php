<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Adapter;

use DateTime;
use DateTimeImmutable;
use Serendipity\Infrastructure\Adapter\SerializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\MongoArrayToEntity;
use Serendipity\Infrastructure\Repository\Formatter\MongoDateTimeToEntity;

class MongoSerializerFactory extends SerializerFactory
{
    protected function converters(): array
    {
        return [
            DateTime::class => new MongoDateTimeToEntity(),
            DateTimeImmutable::class => new MongoDateTimeToEntity(),
            'array' => new MongoArrayToEntity(),
        ];
    }
}
