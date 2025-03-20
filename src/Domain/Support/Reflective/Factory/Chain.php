<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective\Factory;

use ReflectionParameter;

abstract class Chain extends Ruler
{
    protected ?Chain $previous = null;

    public function resolve(ReflectionParameter $parameter, Ruleset $rules): Ruleset
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $rules);
        }
        return $rules;
    }

    final public function then(Chain $resolver): Chain
    {
        $resolver->previous($this);
        return $resolver;
    }

    protected function previous(Chain $previous): void
    {
        $this->previous = $previous;
    }
}
