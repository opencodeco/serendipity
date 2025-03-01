<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Presentation\Output\Accepted;
use Serendipity\Test\TestCase;

final class AcceptedTest extends TestCase
{
    public function testShouldHaveTokenOnContent(): void
    {
        $token = $this->faker()->uuid();
        $output = new Accepted($token);
        $this->assertEquals($token, $output->values()->get('token'));
    }
}
