<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Infrastructure\Repository\Mongo;

use Serendipity\Infrastructure\Repository\MongoRepository;

abstract class MongoGameRepository extends MongoRepository
{
    protected function resource(): string
    {
        return 'games';
    }
}
