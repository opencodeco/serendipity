<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Presentation\Output\NoContent;
use Serendipity\Test\TestCase;


final class NoContentTest extends TestCase
{
    public function testShouldHaveIdOnContent(): void
    {
        $word = $this->faker()->word();
        $properties = ['word' => $word];
        $output = new NoContent($properties);
        $this->assertNull($output->values());
        $this->assertEquals($properties, $output->properties()->toArray());
    }
}
