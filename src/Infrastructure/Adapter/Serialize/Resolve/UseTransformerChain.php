<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

class UseTransformerChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $type = $parameter->getType();
        if (! $type instanceof ReflectionNamedType) {
            return parent::resolve($parameter, $set);
        }
        $conversor = $this->formatter($type->getName());
        if ($conversor === null) {
            return parent::resolve($parameter, $set);
        }
        $value = $conversor->format($set->get($this->name($parameter)));
        return new Value($value);
    }
}
