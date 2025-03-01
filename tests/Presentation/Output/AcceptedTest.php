<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Hyperf\Testing\CanMake;
use Serendipity\Presentation\Output\Accepted;
use PHPUnit\Framework\TestCase;
use Serendipity\Testing\CanFake;

final class AcceptedTest extends TestCase
{
    use CanMake;
    use CanFake;

    public function testShouldHaveTokenOnContent(): void
    {
        $token = $this->generator()->uuid();
        $output = new Accepted($token);
        $this->assertEquals($token, $output->values()->get('token'));
    }
}
