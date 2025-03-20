<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Support\Reflective\Factory\Ruler;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Serendipity\Domain\Support\Reflective\Factory\Ruler\MandatoryChain;
use Serendipity\Domain\Support\Reflective\Ruleset;
use Serendipity\Example\Game\Domain\Entity\Command\GameCommand;

/**
 * @internal
 */
class MandatoryChainTest extends TestCase
{
    public function testRequiredParameterResolution(): void
    {
        $chain = new MandatoryChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(GameCommand::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $chain->resolve($parameters[0], $ruleset);

        $this->assertEquals(['required'], $ruleset->get('name'));
    }

    public function testOptionalParameterResolution(): void
    {
        $chain = new MandatoryChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(GameCommand::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $chain->resolve($parameters[2], $ruleset);

        $this->assertEquals(['sometimes'], $ruleset->get('data'));
    }
}
