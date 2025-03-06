<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository;

use Serendipity\Infrastructure\Database\Document\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Database\Managed;
use SleekDB\Exceptions\InvalidArgumentException;
use SleekDB\Exceptions\InvalidConfigurationException;
use SleekDB\Exceptions\IOException;
use SleekDB\Store;

abstract class SleekDBRepository extends Repository
{
    protected readonly Store $database;

    /**
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     */
    public function __construct(
        protected readonly Managed $generator,
        SleekDBDatabaseFactory $databaseFactory,
    ) {
        $this->database = $databaseFactory->make($this->resource());
    }

    abstract protected function resource(): string;
}
