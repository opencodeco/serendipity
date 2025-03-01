<?php

declare(strict_types=1);

namespace Serendipity\Test;

use Serendipity\Hyperf\Testing\PostgresHelper;
use Serendipity\Testing\CanAssertResource;
use Serendipity\Testing\Resource\SleekDBHelper;

/**
 * @SuppressWarnings(ExcessiveClassLength)
 */
class IntegrationTestCase extends TestCase
{
    use CanAssertResource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpHelper('sleek', $this->make(SleekDBHelper::class));
        $this->setUpHelper('postgres', $this->make(PostgresHelper::class));

        $this->setUpResource('games', 'postgres');

        $this->setUpResource('games', 'sleek');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tearDownResources();
    }
}
