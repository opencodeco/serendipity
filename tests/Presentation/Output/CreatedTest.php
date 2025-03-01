<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Presentation\Output\Created;
use Serendipity\Testing\Extension\FakerExtension;

/**
 * @internal
 */
final class CreatedTest extends TestCase
{
    use MakeExtension;
    use FakerExtension;

    public function testShouldHaveIdOnContent(): void
    {
        $id = $this->generator()->uuid();
        $output = new Created($id);
        $this->assertEquals($id, $output->content()->get('id'));
    }
}
