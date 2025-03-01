<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Presentation\Output\NotFound;
use Serendipity\Test\TestCase;

final class NotFoundTest extends TestCase
{
    public function testShouldHaveMissingOnContent(): void
    {
        $missing = $this->faker()->word();
        $what = $this->faker()->uuid();
        $properties = ['Missing' => sprintf('"%s" identified by "%s" not found', $missing, $what)];
        $output = new NotFound($missing, $what);
        $this->assertNull($output->values());
        $this->assertEquals($properties, $output->properties()->toArray());
    }
}
