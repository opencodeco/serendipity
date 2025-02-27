<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Infrastructure\Testing\TestCase;
use Serendipity\Presentation\Output\Accepted;

class AcceptedTest extends TestCase
{
    public function testShouldHaveTokenOnContent(): void
    {
        $token = $this->faker->engine->uuid();
        $output = new Accepted($token);
        $this->assertEquals($token, $output->content()->get('token'));
    }
}
