<?php

declare(strict_types=1);

namespace Serendipity\Testing\Example\Game\Infrastructure\Repository;

use Serendipity\Domain\Contract\Adapter\Serializer;
use Serendipity\Infrastructure\Adapter\SerializerFactory;
use Serendipity\Infrastructure\Database\Document\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Database\Instrument;
use Serendipity\Testing\Example\Game\Domain\Collection\GameCollection;
use Serendipity\Testing\Example\Game\Domain\Entity\Game;
use Serendipity\Testing\Example\Game\Domain\Repository\GameQueryRepository;
use SleekDB\Exceptions\InvalidArgumentException;
use SleekDB\Exceptions\IOException;

use function Serendipity\Type\Cast\toArray;

class SleekDBGameQueryRepository extends SleekDBGameRepository implements GameQueryRepository
{
    /**
     * @var Serializer<Game>
     */
    protected readonly Serializer $serializer;

    public function __construct(
        Instrument $generator,
        SleekDBDatabaseFactory $databaseFactory,
        SerializerFactory $serializerFactory,
    ) {
        parent::__construct($generator, $databaseFactory);

        $this->serializer = $serializerFactory->make(Game::class);
    }

    /**
     * @throws InvalidArgumentException
     * @throws IOException
     */
    public function getGame(string $id): ?Game
    {
        $data = $this->database->findBy(['id', '=', $id]);
        if (empty($data)) {
            return null;
        }
        /** @var array<string, mixed> $datum */
        $datum = $data[0];
        return $this->serializer->serialize($datum);
    }

    /**
     * @throws IOException
     * @throws InvalidArgumentException
     */
    public function getGames(array $filters): GameCollection
    {
        $criteria = [];
        foreach ($filters as $key => $value) {
            $criteria[] = [$key, '=', $value];
        }
        /** @var array<array<string, mixed>> $data */
        $data = toArray($this->database->findBy($criteria));
        $collection = new GameCollection();
        foreach ($data as $datum) {
            $collection->push($this->serializer->serialize($datum));
        }
        return $collection;
    }
}
