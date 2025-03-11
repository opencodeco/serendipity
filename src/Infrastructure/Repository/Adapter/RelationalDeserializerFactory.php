<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Adapter;

use DateTime;
use DateTimeImmutable;
use Serendipity\Infrastructure\Adapter\DeserializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\RelationalArrayToJson;
use Serendipity\Infrastructure\Repository\Formatter\RelationalDatetimeToString;

class RelationalDeserializerFactory extends DeserializerFactory
{
    protected function formatters(): array
    {
        return [
            'array' => new RelationalArrayToJson(),
            DateTime::class => new RelationalDatetimeToString(),
            DateTimeImmutable::class => new RelationalDatetimeToString(),
        ];
    }
}
