<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Faker\Generate;

use ReflectionParameter;
use Serendipity\Domain\Support\Value;

final class OptionalChain extends Chain
{
    public function resolve(ReflectionParameter $parameter): ?Value
    {
        if ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
            return new Value($parameter->getDefaultValue());
        }
        if ($parameter->allowsNull()) {
            return new Value(null);
        }
        return parent::resolve($parameter);
    }
}
