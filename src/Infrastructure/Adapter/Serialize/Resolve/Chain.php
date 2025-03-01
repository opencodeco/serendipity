<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionParameter;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Exception\Adapter\Type;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\Builder;

abstract class Chain extends Builder
{
    protected ?Chain $previous = null;

    final public function then(Chain $chain): Chain
    {
        $chain->previous($this);
        return $chain;
    }

    public function resolve(ReflectionParameter $parameter, Set $values): Value
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $values);
        }
        return $this->notResolved(Type::INVALID, $parameter, $values);
    }

    protected function previous(Chain $previous): void
    {
        $this->previous = $previous;
    }

    protected function notResolved(Type $type, ReflectionParameter $parameter, Set $values): Value
    {
        $field = $parameter->getName();
        $value = $values->has($field) ? new Value($values->get($field)) : null;
        return new Value(new NotResolved($type, $field, $value));
    }
}
