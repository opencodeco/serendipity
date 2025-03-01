<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;

class WhenCanConvertUseConverterChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, Values $values): Value
    {
        $type = $parameter->getType();
        if (! $type instanceof ReflectionNamedType) {
            return parent::resolve($parameter, $values);
        }
        $conversor = $this->conversor($type->getName());
        if ($conversor === null) {
            return parent::resolve($parameter, $values);
        }
        $value = $conversor->format($values->get($this->name($parameter)));
        return new Value($value);
    }
}
