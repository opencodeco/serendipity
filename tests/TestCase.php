<?php

declare(strict_types=1);

namespace Serendipity\Test;

use PHPUnit\Framework\TestCase as PHPUnit;
use Serendipity\Hyperf\Testing\HasInput;
use Serendipity\Hyperf\Testing\HasMaker;
use Serendipity\Testing\HasBuilder;
use Serendipity\Testing\HasFaker;

class TestCase extends PHPUnit
{
    use HasMaker;
    use HasFaker;
    use HasBuilder;
    use HasInput;

    protected function tearDown(): void
    {
        parent::tearDown();

        gc_collect_cycles();

        $this->tearDownRequest();
    }
}
