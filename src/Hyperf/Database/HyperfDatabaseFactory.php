<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Database;

use Hyperf\DB\DB as Database;
use Serendipity\Infrastructure\Database\Relational\RelationalDatabase;
use Serendipity\Infrastructure\Database\Relational\RelationalDatabaseFactory;

class HyperfDatabaseFactory implements RelationalDatabaseFactory
{
    public function __construct(private readonly Database $database)
    {
    }

    public function make(string $connection): RelationalDatabase
    {
        return new HyperfDatabase($connection, $this->database::connection($connection));
    }
}
