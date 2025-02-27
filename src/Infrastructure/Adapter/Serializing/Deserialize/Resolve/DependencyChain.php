<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serializing\Deserialize\Resolve;

use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serializing\Deserialize\Chain;

use function is_object;

class DependencyChain extends Chain
{
    public function resolve(mixed $value): Value
    {
        if (! is_object($value)) {
            return parent::resolve($value);
        }
        if (enum_exists($value::class)) {
            return new Value($value->value);
        }
        return new Value($this->demolish($value));
    }
}
