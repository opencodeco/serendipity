<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Presentation\Action;

use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Infrastructure\Adapter\Serialize\Builder;
use Serendipity\Presentation\Output\Accepted;
use Serendipity\Testing\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Testing\Example\Game\Domain\Repository\GameCommandRepository;
use Serendipity\Testing\Example\Game\Presentation\Input\CreateGameInput;

readonly class CreateGameAction
{
    public function __construct(
        private Builder $builder,
        private GameCommandRepository $gameCommandRepository,
    ) {
    }

    /**
     * @throws GeneratingException
     */
    public function __invoke(CreateGameInput $input): Message
    {
        $game = $this->builder->build(GameCommand::class, $input->values());
        $id = $this->gameCommandRepository->persist($game);
        return new Accepted($id);
    }
}
