<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize;

use PHPUnit\Framework\TestCase;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;
use Serendipity\Infrastructure\Adapter\Deserialize\Demolisher;

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
}
