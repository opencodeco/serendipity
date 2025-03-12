<?php

declare(strict_types=1);

namespace Serendipity\Testing\Mock;

use Closure;
use Faker\Generator;
use Serendipity\Testing\Extension\FakerExtension;
use Serendipity\Testing\Faker\Faker;

final class FakerExtensionMock
{
    use FakerExtension;

    public function __construct(
        private readonly Faker $mock,
        private readonly ?Closure $assertion = null,
    ) {
    }

    public function assertFaker(): Faker
    {
        return $this->faker();
    }

    public function assertGenerator(): Generator
    {
        return $this->generator();
    }

    protected function make(string $class, array $args = []): mixed
    {
        if ($this->assertion !== null) {
            invoke($this->assertion, $class);
        }
        /* @phpstan-ignore return.type */
        return $this->mock;
    }
}
