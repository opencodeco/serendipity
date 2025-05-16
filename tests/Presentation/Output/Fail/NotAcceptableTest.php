<?php

declare(strict_types=1);

namespace Serendipity\Test\Presentation\Output\Fail;

use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Presentation\Output\Fail\NotAcceptable;
use Serendipity\Testing\Extension\FakerExtension;

final class NotAcceptableTest extends TestCase
{
    use MakeExtension;
    use FakerExtension;

    public function testShouldHaveTokenOnContent(): void
    {
        $token = $this->generator()->uuid();
        $output = NotAcceptable::createFrom($token);
        $this->assertEquals($token, $output->content());
        $this->assertEquals(['token' => $token], $output->properties()->toArray());
    }
}
