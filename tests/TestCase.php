<?php

declare(strict_types=1);

namespace Serendipity\Test;

use PHPUnit\Framework\TestCase as PHPUnit;
use Serendipity\Hyperf\Testing\CanMakeInput;
use Serendipity\Hyperf\Testing\CanMake;
use Serendipity\Testing\CanBuild;
use Serendipity\Testing\CanFake;

class TestCase extends PHPUnit
{
    use CanMake;
    use CanFake;
    use CanBuild;
    use CanMakeInput;

    protected function tearDown(): void
    {
        parent::tearDown();

        gc_collect_cycles();

        $this->tearDownRequest();
    }
}
