<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Testing\Example\Game;

use Serendipity\Hyperf\Testing\Extension\InputExtension;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Hyperf\Testing\PostgresHelper;
use Serendipity\Test\Testing\ExtensibleTestCase;
use Serendipity\Testing\Extension\FakerExtension;
use Serendipity\Testing\Extension\ResourceExtension;
use Serendipity\Testing\Resource\SleekDBHelper;

/**
 * @internal
 */
class PresentationTestCase extends ExtensibleTestCase
{
    use MakeExtension;
    use FakerExtension;
    use ResourceExtension;
    use InputExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpInput();

        $this->setUpResourceHelper('sleek', $this->make(SleekDBHelper::class));
        $this->setUpResourceHelper('postgres', $this->make(PostgresHelper::class));
    }
}
