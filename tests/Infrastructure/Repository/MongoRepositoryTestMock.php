<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Repository;

use Serendipity\Infrastructure\Repository\MongoRepository;

class MongoRepositoryTestMock extends MongoRepository
{
    protected function resource(): string
    {
        return 'x';
    }
}
