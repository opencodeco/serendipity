<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Presentation\Output\Fail\NotFound;
use Serendipity\Testing\Extension\FakerExtension;

final class NotFoundTest extends TestCase
{
    use MakeExtension;
    use FakerExtension;

    public function testShouldHaveMissingOnContent(): void
    {
        $missing = $this->generator()->word();
        $what = $this->generator()->uuid();
        $properties = ['Missing' => sprintf('"%s" identified by "%s" not found', $missing, $what)];
        $output = NotFound::createFrom($missing, $what);
        $this->assertNull($output->content());
        $this->assertEquals($properties, $output->properties()->toArray());
    }
}
