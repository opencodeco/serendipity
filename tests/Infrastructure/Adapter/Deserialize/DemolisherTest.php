<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Contract\Exportable;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Infrastructure\Adapter\Deserialize\Demolisher;

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
}
