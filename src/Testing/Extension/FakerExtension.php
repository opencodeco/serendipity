<?php

declare(strict_types=1);

namespace Serendipity\Testing\Extension;

use Faker\Generator;
use Serendipity\Testing\Faker\Faker;

/**
 * @phpstan-ignore trait.unused
 */
trait FakerExtension
{
    private ?Faker $faker = null;

    protected function faker(): Faker
    {
        if ($this->faker === null) {
            $this->faker = $this->make(Faker::class);
        }
        return $this->faker;
    }

    protected function generator(): Generator
    {
        return $this->faker()->generator();
    }

    abstract protected function make(string $class, array $args = []): mixed;
}
