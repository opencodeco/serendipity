<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Database;

use Hyperf\DB\DB as Database;

class HyperfDBFactory
{
    public function __construct(private readonly Database $database)
    {
    }

    public function make(string $connection): HyperfDatabase
    {
        return new HyperfDatabase($connection, $this->database::connection($connection));
    }
}
