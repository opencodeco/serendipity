<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Presentation\Output\NotFound;
use Serendipity\Test\TestCase;

final class NotFoundTest extends TestCase
{
    public function testShouldHaveMissingOnContent(): void
    {
        $missing = $this->faker->engine->word();
        $what = $this->faker->engine->uuid();
        $properties = ['Missing' => sprintf('"%s" identified by "%s" not found', $missing, $what)];
        $output = new NotFound($missing, $what);
        $this->assertNull($output->content());
        $this->assertEquals($properties, $output->properties()->toArray());
    }
}
