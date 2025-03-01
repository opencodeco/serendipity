<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Presentation\Output\Created;
use Serendipity\Test\TestCase;


final class CreatedTest extends TestCase
{
    public function testShouldHaveIdOnContent(): void
    {
        $id = $this->generator()->uuid();
        $output = new Created($id);
        $this->assertEquals($id, $output->values()->get('id'));
    }
}
