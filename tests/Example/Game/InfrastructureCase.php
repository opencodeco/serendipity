<?php

declare(strict_types=1);

namespace Serendipity\Test\Example\Game;

use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Hyperf\Testing\PostgresHelper;
use Serendipity\Test\Testing\ExtensibleCase;
use Serendipity\Testing\Extension\FakerExtension;
use Serendipity\Testing\Extension\ResourceExtension;
use Serendipity\Testing\Resource\SleekDBHelper;

/**
 * @internal
 */
abstract class InfrastructureCase extends ExtensibleCase
{
    use MakeExtension;
    use FakerExtension;
    use ResourceExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpResourceHelper('sleek', $this->make(SleekDBHelper::class));
        $this->setUpResourceHelper('postgres', $this->make(PostgresHelper::class));
    }
}
