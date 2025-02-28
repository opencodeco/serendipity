<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Persistence\Serializing;

use DateTime;
use DateTimeImmutable;
use Serendipity\Infrastructure\Adapter\Serializing\DeserializerFactory;
use Serendipity\Infrastructure\Persistence\Converter\FromArrayToDatabase;
use Serendipity\Infrastructure\Persistence\Converter\FromDatetimeToDatabase;

class RelationalDeserializerFactory extends DeserializerFactory
{
    protected function converters(): array
    {
        return [
            'array' => new FromArrayToDatabase(),
            DateTime::class => new FromDatetimeToDatabase(),
            DateTimeImmutable::class => new FromDatetimeToDatabase(),
        ];
    }
}
