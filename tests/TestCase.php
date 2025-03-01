<?php

declare(strict_types=1);

namespace Serendipity\Test;

use PHPUnit\Framework\TestCase as PHPUnit;
use Serendipity\Testing\HelperFactory;

class TestCase extends PHPUnit
{
    use HelperFactory;

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->collectGarbage();
    }
}
