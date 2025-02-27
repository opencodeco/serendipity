<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Infrastructure\Testing\TestCase;
use Serendipity\Presentation\Output\Created;


final class CreatedTest extends TestCase
{
    public function testShouldHaveIdOnContent(): void
    {
        $id = $this->faker->engine->uuid();
        $output = new Created($id);
        $this->assertEquals($id, $output->content()->get('id'));
    }
}
