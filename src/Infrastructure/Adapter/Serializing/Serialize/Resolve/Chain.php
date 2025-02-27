<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve;

use ReflectionParameter;
use Serendipity\Domain\Exception\Mapping\NotResolved;
use Serendipity\Domain\Exception\Mapping\NotResolvedType;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\Adapter\Serializing\Serialize\Builder;

abstract class Chain extends Builder
{
    protected ?Chain $previous = null;

    final public function then(Chain $chain): Chain
    {
        $chain->previous($this);
        return $chain;
    }

    public function resolve(ReflectionParameter $parameter, Values $values): Value
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $values);
        }
        return $this->notResolved(NotResolvedType::INVALID, $parameter, $values);
    }

    protected function previous(Chain $previous): void
    {
        $this->previous = $previous;
    }

    protected function notResolved(NotResolvedType $type, ReflectionParameter $parameter, Values $values): Value
    {
        $field = $parameter->getName();
        $value = $values->has($field) ? new Value($values->get($field)) : null;
        return new Value(new NotResolved($type, $field, $value));
    }
}
