<?php

namespace Serendipity\Infrastructure\Testing\Faker\Provider;

use Serendipity\Infrastructure\Persistence\Generator;

class PersistenceProvider
{
    public function __construct(private readonly Generator $generator)
    {
    }

    public function id(): string
    {
        return $this->generator->id();
    }

    public function updatedAt(): string
    {
        return $this->generator->now();
    }

    public function createdAt(): string
    {
        return $this->generator->now();
    }
}
