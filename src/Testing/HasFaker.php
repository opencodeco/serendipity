<?php

declare(strict_types=1);

namespace Serendipity\Testing;

use Serendipity\Testing\Faker\Faker;

/**
 * @phpstan-ignore trait.unused
 */
trait HasFaker
{
    protected ?Faker $faker = null;

    protected function faker(): Faker
    {
        if ($this->faker === null) {
            $this->faker = $this->make(Faker::class);
        }
        return $this->faker;
    }

    abstract protected function make(string $class, array $args = []): mixed;
}
