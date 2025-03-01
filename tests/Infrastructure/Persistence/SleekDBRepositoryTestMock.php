<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Persistence;

use Serendipity\Infrastructure\Repository\SleekDBRepository;

class SleekDBRepositoryTestMock extends SleekDBRepository
{
    protected function resource(): string
    {
        return 'x';
    }
}
