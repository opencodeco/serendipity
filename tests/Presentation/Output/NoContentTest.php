<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Hyperf\Testing\CanMake;
use Serendipity\Presentation\Output\NoContent;
use PHPUnit\Framework\TestCase;
use Serendipity\Testing\CanFake;


final class NoContentTest extends TestCase
{
    use CanMake;
    use CanFake;

    public function testShouldHaveIdOnContent(): void
    {
        $word = $this->generator()->word();
        $properties = ['word' => $word];
        $output = new NoContent($properties);
        $this->assertNull($output->values());
        $this->assertEquals($properties, $output->properties()->toArray());
    }
}
