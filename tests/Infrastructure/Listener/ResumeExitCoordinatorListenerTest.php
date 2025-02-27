<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Listener;

use Hyperf\Command\Event\AfterExecute;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Serendipity\Infrastructure\Listener\ResumeExitCoordinatorListener;
use Serendipity\Infrastructure\Testing\TestCase;

class ResumeExitCoordinatorListenerTest extends TestCase
{
    public function testShouldListenToAfterExecuteEvent(): void
    {
        $listener = new ResumeExitCoordinatorListener();
        $this->assertEquals([AfterExecute::class], $listener->listen());
    }

    public function testShouldResumeCoordinatorOnProcess(): void
    {
        $listener = new ResumeExitCoordinatorListener();
        $event = $this->createMock(AfterExecute::class);

        $listener->process($event);

        $this->assertTrue(CoordinatorManager::until(Constants::WORKER_EXIT)->isClosing());
    }
}
