<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Infrastructure\Repository\SleekDB;

use Serendipity\Example\Game\Domain\Collection\GameCollection;
use Serendipity\Example\Game\Domain\Entity\Game;
use Serendipity\Example\Game\Domain\Repository\GameQueryRepository;
use Serendipity\Infrastructure\Adapter\SerializerFactory;
use Serendipity\Infrastructure\Database\Document\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Database\Managed;
use SleekDB\Exceptions\InvalidArgumentException;
use SleekDB\Exceptions\IOException;

use function Serendipity\Type\Cast\toArray;

class SleekDBGameQueryRepository extends SleekDBGameRepository implements GameQueryRepository
{
    public function __construct(
        Managed $generator,
        SleekDBDatabaseFactory $databaseFactory,
        protected readonly SerializerFactory $serializerFactory,
    ) {
        parent::__construct($generator, $databaseFactory);
    }

    /**
     * @throws InvalidArgumentException
     * @throws IOException
     */
    public function getGame(string $id): ?Game
    {
        $data = toArray($this->database->findBy(['id', '=', $id]));
        $serializer = $this->serializerFactory->make(Game::class);
        return $this->entity($serializer, $data);
    }

    /**
     * @throws IOException
     * @throws InvalidArgumentException
     */
    public function getGames(array $filters = []): GameCollection
    {
        $serializer = $this->serializerFactory->make(Game::class);
        if (empty($filters)) {
            $data = toArray($this->database->findAll());
            return $this->collection($serializer, $data, GameCollection::class);
        }
        $criteria = [];
        foreach ($filters as $key => $value) {
            $criteria[] = [$key, '=', $value];
        }
        $data = toArray($this->database->findBy($criteria));
        return $this->collection($serializer, $data, GameCollection::class);
    }
}
