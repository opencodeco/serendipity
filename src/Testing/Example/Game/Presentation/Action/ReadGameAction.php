<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Presentation\Action;

use Serendipity\Domain\Contract\Message;
use Serendipity\Presentation\Output\NotFound;
use Serendipity\Presentation\Output\Ok;
use Serendipity\Testing\Example\Game\Domain\Entity\Game;
use Serendipity\Testing\Example\Game\Domain\Repository\GameQueryRepository;
use Serendipity\Testing\Example\Game\Presentation\Input\ReadGameInput;

readonly class ReadGameAction
{
    public function __construct(private GameQueryRepository $gameQueryRepository)
    {
    }

    public function __invoke(ReadGameInput $input): Message
    {
        $id = $input->value('id', '');
        $game = $this->gameQueryRepository->getGame($id);
        return $game
            ? Ok::createFrom($game)
            : NotFound::createFrom(Game::class, $id);
    }
}
