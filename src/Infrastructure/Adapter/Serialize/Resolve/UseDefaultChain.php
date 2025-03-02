<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Exception\Adapter\Type;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

class UseDefaultChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $values): Value
    {
        $name = $this->name($parameter);
        if ($values->has($name)) {
            return parent::resolve($parameter, $values);
        }
        return $this->resolveNoValue($parameter, $values);
    }

    /**
     * @throws ReflectionException
     */
    public function resolveNoValue(ReflectionParameter $parameter, Set $values): Value
    {
        if ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
            return new Value($parameter->getDefaultValue());
        }
        if ($parameter->allowsNull()) {
            return new Value(null);
        }
        return $this->notResolved(Type::REQUIRED, $parameter, $values);
    }
}
