<?php

declare(strict_types=1);

namespace Serendipity\Test\Example\Game\Presentation;

use Serendipity\Example\Game\Domain\Entity\Game;
use Serendipity\Example\Game\Presentation\Action\ReadGameAction;
use Serendipity\Example\Game\Presentation\Input\ReadGameInput;
use Serendipity\Presentation\Output\Fail\NotFound;
use Serendipity\Presentation\Output\Ok;
use Serendipity\Test\Example\Game\PresentationCase;

/**
 * @internal
 */
class ReadGameActionTest extends PresentationCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpResource('games', 'sleek');
    }

    final public function testShouldReturnOk(): void
    {
        $values = $this->seed(Game::class);

        $input = $this->input(class: ReadGameInput::class, params: ['id' => $values->get('id')]);

        $action = $this->make(ReadGameAction::class);
        $actual = $action($input);

        $this->assertInstanceOf(Ok::class, $actual);
        $this->assertSame($values->get('id'), $actual->content()->id);
        $this->assertSame($values->get('name'), $actual->content()->name);
    }

    final public function testShouldReturnNotFound(): void
    {
        $input = $this->input(class: ReadGameInput::class, params: ['id' => $this->generator()->uuid()]);

        $action = $this->make(ReadGameAction::class);
        $actual = $action($input);

        $this->assertInstanceOf(NotFound::class, $actual);
    }
}
