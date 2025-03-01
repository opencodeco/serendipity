<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Testing\Example\Game\Presentation;

use Serendipity\Presentation\Output\Accepted;
use Serendipity\Test\Hyperf\Testing\Example\Game\PresentationTestCase;
use Serendipity\Testing\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Testing\Example\Game\Presentation\Action\CreateGameAction;
use Serendipity\Testing\Example\Game\Presentation\Input\CreateGameInput;

/**
 * @internal
 */
class CreateGameActionTest extends PresentationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpResource('games', 'sleek');
    }

    final public function testCreateGameSuccessfully(): void
    {
        $values = $this->faker()->fake(GameCommand::class);
        $input = $this->input(CreateGameInput::class, $values->toArray());
        $action = $this->make(CreateGameAction::class);
        $result = $action($input);
        $this->assertInstanceOf(Accepted::class, $result);
        $this->assertIsString($result->content());
    }
}
