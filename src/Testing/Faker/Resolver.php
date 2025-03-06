<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

abstract class Resolver extends Faker
{
    protected ?Resolver $previous = null;

    final public function then(Resolver $resolver): Resolver
    {
        $resolver->previous($this);
        return $resolver;
    }

    public function resolve(ReflectionParameter $parameter, Set $preset): ?Value
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $preset);
        }
        return null;
    }

    final protected function previous(Resolver $previous): void
    {
        $this->previous = $previous;
    }
}
