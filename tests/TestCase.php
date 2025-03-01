<?php

declare(strict_types=1);

namespace Serendipity\Test;

use PHPUnit\Framework\TestCase as PHPUnit;
use Serendipity\Infrastructure\Adapter\Serialize\Builder;
use Serendipity\Infrastructure\Testing\Faker\Faker;
use Serendipity\Infrastructure\Testing\HelperFactory;

class TestCase extends PHPUnit
{
    use HelperFactory;

    protected Faker $faker;

    protected Builder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = $this->make(Faker::class);
        $this->builder = $this->make(Builder::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->collectGarbage();
    }
}
