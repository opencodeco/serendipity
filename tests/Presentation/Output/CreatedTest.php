<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use Serendipity\Hyperf\Testing\CanMake;
use Serendipity\Presentation\Output\Created;
use PHPUnit\Framework\TestCase;
use Serendipity\Testing\CanFake;


final class CreatedTest extends TestCase
{
    use CanMake;
    use CanFake;

    public function testShouldHaveIdOnContent(): void
    {
        $id = $this->generator()->uuid();
        $output = new Created($id);
        $this->assertEquals($id, $output->values()->get('id'));
    }
}
