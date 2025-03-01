<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output;

use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Presentation\Output\Accepted;
use Serendipity\Testing\Extension\FakerExtension;

/**
 * @internal
 */
final class AcceptedTest extends TestCase
{
    use MakeExtension;
    use FakerExtension;

    public function testShouldHaveTokenOnContent(): void
    {
        $token = $this->generator()->uuid();
        $output = new Accepted($token);
        $this->assertEquals($token, $output->content()->get('token'));
    }
}
