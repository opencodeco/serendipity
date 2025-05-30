<?php

declare(strict_types=1);

namespace Serendipity\Example\Game\Infrastructure\Repository\Mongo;

use MongoDB\Model\BSONDocument;
use Serendipity\Example\Game\Domain\Collection\GameCollection;
use Serendipity\Example\Game\Domain\Entity\Game;
use Serendipity\Example\Game\Domain\Repository\GameQueryRepository;
use Serendipity\Infrastructure\Database\Document\MongoFactory;
use Serendipity\Infrastructure\Database\Managed;
use Serendipity\Infrastructure\Repository\Adapter\MongoSerializerFactory;

class MongoGameQueryRepository extends MongoGameRepository implements GameQueryRepository
{
    public function __construct(
        Managed $generator,
        MongoFactory $mongoFactory,
        protected readonly MongoSerializerFactory $serializerFactory,
    ) {
        parent::__construct($generator, $mongoFactory);
    }

    public function getGame(string $id): ?Game
    {
        $result = $this->collection->find(['id' => $id]);
        $data = $result->toArray();
        if (empty($data)) {
            return null;
        }
        $serializer = $this->serializerFactory->make(Game::class);
        return $this->entity($serializer, $data);
    }

    public function getGames(array $filters = []): GameCollection
    {
        $serializer = $this->serializerFactory->make(Game::class);
        $result = $this->collection->find($filters);
        $data = $result->toArray();
        return $this->collection($serializer, $data, GameCollection::class);
    }

    /**
     * @return array<string, mixed>
     */
    protected function normalize(mixed $datum): array
    {
        return match (true) {
            $datum instanceof BSONDocument => $datum->getArrayCopy(),
            default => parent::normalize($datum),
        };
    }
}
