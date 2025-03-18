<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize\Resolve;

use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;

class DoNothingChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        return new Value($value);
    }
}
