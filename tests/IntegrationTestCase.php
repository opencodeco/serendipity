<?php

declare(strict_types=1);

namespace Serendipity\Test;

use Serendipity\Hyperf\Testing\PostgresHelper;
use Serendipity\Testing\HasResource;
use Serendipity\Testing\Resource\SleekDBHelper;

/**
 * @SuppressWarnings(ExcessiveClassLength)
 */
class IntegrationTestCase extends TestCase
{
    use HasResource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helper('sleek', $this->make(SleekDBHelper::class));
        $this->helper('postgres', $this->make(PostgresHelper::class));

        $this->resource('games', 'postgres');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tearDownResources();
    }
}
