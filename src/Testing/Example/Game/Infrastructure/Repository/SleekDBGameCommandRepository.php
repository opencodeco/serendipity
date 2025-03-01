<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Infrastructure\Repository;

use Serendipity\Domain\Contract\Adapter\Deserializer;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Infrastructure\Adapter\DeserializerFactory;
use Serendipity\Infrastructure\Database\Document\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Database\Instrument;
use Serendipity\Testing\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Testing\Example\Game\Domain\Repository\GameCommandRepository;
use SleekDB\Exceptions\IdNotAllowedException;
use SleekDB\Exceptions\InvalidArgumentException;
use SleekDB\Exceptions\IOException;
use SleekDB\Exceptions\JsonException;

class SleekDBGameCommandRepository extends SleekDBGameRepository implements GameCommandRepository
{
    /**
     * @var Deserializer<GameCommand>
     */
    protected readonly Deserializer $deserializer;

    public function __construct(
        Instrument $generator,
        SleekDBDatabaseFactory $databaseFactory,
        DeserializerFactory $deserializerFactory,
    ) {
        parent::__construct($generator, $databaseFactory);

        $this->deserializer = $deserializerFactory->make(GameCommand::class);
    }

    /**
     * @throws IOException
     * @throws JsonException
     * @throws IdNotAllowedException
     * @throws InvalidArgumentException
     * @throws GeneratingException
     */
    public function persist(GameCommand $game): string
    {
        $datum = $this->deserializer->deserialize($game);
        $id = $this->generator->id();
        $datum['id'] = $id;
        $datum['created_at'] = $this->generator->now();
        $datum['updated_at'] = $this->generator->now();
        $this->database->insert($datum);
        return $id;
    }
}
