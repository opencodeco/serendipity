<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Hyperf\Testing\CanMake;
use Serendipity\Presentation\Output\NotFound;
use PHPUnit\Framework\TestCase;
use Serendipity\Testing\CanFake;

final class NotFoundTest extends TestCase
{
    use CanMake;
    use CanFake;

    public function testShouldHaveMissingOnContent(): void
    {
        $missing = $this->generator()->word();
        $what = $this->generator()->uuid();
        $properties = ['Missing' => sprintf('"%s" identified by "%s" not found', $missing, $what)];
        $output = new NotFound($missing, $what);
        $this->assertNull($output->values());
        $this->assertEquals($properties, $output->properties()->toArray());
    }
}
