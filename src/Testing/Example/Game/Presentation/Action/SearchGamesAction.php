<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Presentation\Action;

use Serendipity\Domain\Contract\Message;
use Serendipity\Presentation\Output\Ok;
use Serendipity\Testing\Example\Game\Domain\Repository\GameQueryRepository;
use Serendipity\Testing\Example\Game\Presentation\Input\SearchGamesInput;

readonly class SearchGamesAction
{
    public function __construct(private GameQueryRepository $gameQueryRepository)
    {
    }

    public function __invoke(SearchGamesInput $input): Message
    {
        $name = $input->value('name');
        $slug = $input->value('slug');
        $games = $this->gameQueryRepository->getGames([
            'names' => $name,
            'slug' => $slug,
        ]);
        return Ok::createFrom($games);
    }
}
