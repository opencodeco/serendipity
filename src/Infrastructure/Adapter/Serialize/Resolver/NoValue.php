<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver;

class NoValue extends Resolver
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $name = $this->casedName($parameter);
        if ($set->has($name)) {
            return parent::resolve($parameter, $set);
        }
        return $this->resolveNoValue($parameter);
    }

    /**
     * @throws ReflectionException
     */
    public function resolveNoValue(ReflectionParameter $parameter): Value
    {
        if ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
            return new Value($parameter->getDefaultValue());
        }
        if ($parameter->allowsNull()) {
            return new Value(null);
        }
        return $this->notResolvedAsRequired();
    }
}
