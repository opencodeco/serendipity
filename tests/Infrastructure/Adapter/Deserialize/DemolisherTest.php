<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Collection\Collection;
use Serendipity\Domain\Contract\Exportable;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Infrastructure\Adapter\Deserialize\Demolisher;
use stdClass;

/**
 * @internal
 */
final class DemolisherTest extends TestCase
{
    public function testShouldDemolish(): void
    {
        $demolisher = new Demolisher(formatters: [
            'string' => fn ($value) => sprintf('[%s]', $value),
        ]);
        $instance = new GameCommand('Cool game', 'cool-game');
        $values = $demolisher->demolish($instance);

        $this->assertEquals('[Cool game]', $values['name']);
        $this->assertEquals('[cool-game]', $values['slug']);
    }

    public function testShouldNotUseInvalidNovaValueParameter(): void
    {
        $demolisher = new Demolisher();
        $instance = new readonly class implements Exportable {
            public function __construct(public string $name = 'Jhon Doe')
            {
            }

            public function export(): array
            {
                return ['title' => $this->name];
            }
        };
        $values = $demolisher->demolish($instance);
        $this->assertEmpty($values);
    }

    public function testShouldDemolishCollection(): void
    {
        // Create a concrete implementation of Collection for testing
        $collection = new class extends Collection {
            public function current(): GameCommand
            {
                return $this->datum();
            }

            protected function validate(mixed $datum): GameCommand
            {
                if ($datum instanceof GameCommand) {
                    return $datum;
                }
                throw $this->exception(GameCommand::class, $datum);
            }
        };

        // Add some Exportable objects to the collection
        $collection->push(new GameCommand('Game 1', 'game-1'));
        $collection->push(new GameCommand('Game 2', 'game-2'));

        // Test demolishCollection method
        $demolisher = new Demolisher(formatters: [
            'string' => fn ($value) => sprintf('[%s]', $value),
        ]);
        $demolished = $demolisher->demolishCollection($collection);

        // Verify results
        $this->assertCount(2, $demolished);
        $this->assertEquals('[Game 1]', $demolished[0]['name']);
        $this->assertEquals('[game-1]', $demolished[0]['slug']);
        $this->assertEquals('[Game 2]', $demolished[1]['name']);
        $this->assertEquals('[game-2]', $demolished[1]['slug']);
    }

    public function testShouldHandleMixedItemsInCollection(): void
    {
        // Create a concrete implementation of Collection that can hold mixed items
        $collection = new class extends Collection {
            public function current(): GameCommand|stdClass
            {
                return $this->datum();
            }

            protected function validate(mixed $datum): GameCommand|stdClass
            {
                return $datum;
            }
        };

        // Add an Exportable object and a non-Exportable object to the collection
        $collection->push(new GameCommand('Game 1', 'game-1'));
        $collection->push(new stdClass());

        // Test demolishCollection method
        $demolisher = new Demolisher(formatters: [
            'string' => fn ($value) => sprintf('[%s]', $value),
        ]);
        $demolished = $demolisher->demolishCollection($collection);

        // Verify results
        $this->assertCount(2, $demolished);
        $this->assertEquals('[Game 1]', $demolished[0]['name']);
        $this->assertEquals('[game-1]', $demolished[0]['slug']);
        $this->assertInstanceOf(stdClass::class, $demolished[1]);
    }
}
