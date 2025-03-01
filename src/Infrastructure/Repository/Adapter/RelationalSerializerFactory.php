<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Adapter;

use Serendipity\Infrastructure\Adapter\SerializerFactory;
use Serendipity\Infrastructure\Repository\Formatter\FromDatabaseToArray;

class RelationalSerializerFactory extends SerializerFactory
{
    protected function converters(): array
    {
        return ['array' => new FromDatabaseToArray()];
    }
}
