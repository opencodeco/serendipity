<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Generate;

use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Set;

final class GenerateFromDefaultValueChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, ?Set $preset = null): ?Value
    {
        if ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
            return new Value($parameter->getDefaultValue());
        }
        if ($parameter->allowsNull()) {
            return new Value(null);
        }
        return parent::resolve($parameter, $preset);
    }
}
