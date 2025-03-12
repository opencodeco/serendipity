<?php

declare(strict_types=1);

namespace Serendipity\Testing\Extension;

use Faker\Generator;
use Serendipity\Testing\Faker\Faker;

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

    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     */
    abstract protected function make(string $class, array $args = []): mixed;
}
