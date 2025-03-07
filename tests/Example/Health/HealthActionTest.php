<?php

declare(strict_types=1);

namespace Serendipity\Test\Example\Health;

use Serendipity\Example\Health\HealthAction;
use Serendipity\Example\Health\HealthInput;
use Serendipity\Hyperf\Testing\Extension\InputExtension;
use Serendipity\Hyperf\Testing\Extension\LoggerExtension;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Test\Testing\ExtensibleCase;
use Serendipity\Testing\Extension\FakerExtension;

/**
 * @internal
 */
final class HealthActionTest extends ExtensibleCase
{
    use MakeExtension;
    use InputExtension;
    use FakerExtension;
    use LoggerExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpInput();
        $this->setUpLogger();
    }

    public function testHealthAction(): void
    {
        $message = $this->generator()->word();
        $input = $this->input(HealthInput::class, ['message' => $message]);
        $action = $this->make(HealthAction::class);
        $result = $action($input);
        $this->assertEquals($message, $result);
        $this->assertLogged('/Health action message: .*/', 'emergency');
        $this->assertLogged('/Health action message: .*/', 'alert');
        $this->assertLogged('/Health action message: .*/', 'critical');
        $this->assertLogged('/Health action message: .*/', 'error');
        $this->assertLogged('/Health action message: .*/', 'warning');
        $this->assertLogged('/Health action message: .*/', 'notice');
        $this->assertLogged('/Health action message: .*/', 'info');
        $this->assertLogged('/Health action message: .*/', 'debug');
    }
}
