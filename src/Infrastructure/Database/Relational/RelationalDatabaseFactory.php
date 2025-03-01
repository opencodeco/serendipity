<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Database\Relational;

interface RelationalDatabaseFactory
{
    public function make(string $connection): RelationalDatabase;
}
