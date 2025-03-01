<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Serializing;

use DateTime;
use DateTimeImmutable;
use Serendipity\Infrastructure\Adapter\DeserializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\FromArrayToDatabase;
use Serendipity\Infrastructure\Repository\Formatter\FromDatetimeToDatabase;

class RelationalDeserializerFactory extends DeserializerFactory
{
    protected function formatters(): array
    {
        return [
            'array' => new FromArrayToDatabase(),
            DateTime::class => new FromDatetimeToDatabase(),
            DateTimeImmutable::class => new FromDatetimeToDatabase(),
        ];
    }
}
