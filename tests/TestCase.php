<?php

declare(strict_types=1);

namespace Serendipity\Test;

use PHPUnit\Framework\TestCase as PHPUnit;
use Serendipity\Infrastructure\Adapter\Serializing\Serialize\Builder;
use Serendipity\Infrastructure\Testing\Factory;
use Serendipity\Infrastructure\Testing\Faker\Faker;

class TestCase extends PHPUnit
{
    use Factory;

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
