<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Faker\Generate;

use Faker\Generator;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;

abstract class Chain
{
    protected ?Chain $previous = null;

    public function __construct(protected readonly Generator $faker)
    {
    }

    final public function then(Chain $chain): Chain
    {
        $chain->previous($this);
        return $chain;
    }

    public function resolve(ReflectionParameter $parameter): ?Value
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter);
        }
        return null;
    }

    protected function previous(Chain $previous): void
    {
        $this->previous = $previous;
    }
}
