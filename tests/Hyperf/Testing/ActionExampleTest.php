<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Testing;

use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Testing\CanMake;
use Serendipity\Hyperf\Testing\CanMakeInput;
use Serendipity\Test\Hyperf\Testing\Presentation\HealthAction;
use Serendipity\Test\Hyperf\Testing\Presentation\HealthInput;
use Serendipity\Testing\CanFake;

final class ActionExampleTest extends TestCase
{
    use CanMake;
    use CanFake;
    use CanMakeInput;

    private array $callbacks = [];

    protected function tearDown(): void
    {
        foreach ($this->callbacks as $callback) {
            $callback();
        }
        parent::tearDown();
    }

    public function testHealthAction(): void
    {
        $message = $this->generator()->word();
        $input = $this->input(HealthInput::class, ['message' => $message]);
        $action = $this->make(HealthAction::class);
        $result = $action($input);
        $this->assertEquals($message, $result);
    }

    protected function registerTearDown(callable $callback): void
    {
        $this->callbacks[] = $callback;
    }
}
