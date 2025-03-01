<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Testing\Example\Health;

use Serendipity\Hyperf\Testing\Extension\InputExtension;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Test\Testing\ExtensibleTestCase;
use Serendipity\Testing\Example\Health\HealthAction;
use Serendipity\Testing\Example\Health\HealthInput;
use Serendipity\Testing\Extension\FakerExtension;

/**
 * @internal
 */
final class ActionHealthTest extends ExtensibleTestCase
{
    use MakeExtension;
    use InputExtension;
    use FakerExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpInput();
    }

    public function testHealthAction(): void
    {
        $message = $this->generator()->word();
        $input = $this->input(HealthInput::class, ['message' => $message]);
        $action = $this->make(HealthAction::class);
        $result = $action($input);
        $this->assertEquals($message, $result);
    }
}
