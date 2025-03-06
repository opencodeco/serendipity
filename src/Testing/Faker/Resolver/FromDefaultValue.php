<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Resolver;

use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Testing\Faker\Resolver;

final class FromDefaultValue extends Resolver
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        if ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
            return new Value($parameter->getDefaultValue());
        }
        if ($parameter->allowsNull()) {
            return new Value(null);
        }
        return parent::resolve($parameter, $presets);
    }
}
