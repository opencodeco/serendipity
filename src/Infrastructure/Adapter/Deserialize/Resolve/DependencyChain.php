<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize\Resolve;

use BackedEnum;
use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;

use function is_object;

class DependencyChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        if (! is_object($value)) {
            return parent::resolve($parameter, $value);
        }
        if ($value instanceof BackedEnum) {
            return new Value($value->value);
        }
        return new Value((array) $this->demolish($value));
    }
}
