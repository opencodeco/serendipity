<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Database\Relational;

use Hyperf\DB\DB as Database;
use Serendipity\Infrastructure\Database\Relational\Connection;
use Serendipity\Infrastructure\Database\Relational\ConnectionFactory;

class HyperfConnectionFactory implements ConnectionFactory
{
    public function __construct(private readonly Database $database)
    {
    }

    public function make(string $connection): Connection
    {
        return new HyperfConnection($connection, $this->database::connection($connection));
    }
}
