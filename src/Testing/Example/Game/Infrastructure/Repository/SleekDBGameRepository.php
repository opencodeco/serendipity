<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Infrastructure\Repository;

use Serendipity\Infrastructure\Repository\SleekDBRepository;

abstract class SleekDBGameRepository extends SleekDBRepository
{
    protected function resource(): string
    {
        return 'games';
    }
}
