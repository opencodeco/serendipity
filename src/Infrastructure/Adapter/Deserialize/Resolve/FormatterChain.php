<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize\Resolve;

use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;

class FormatterChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $type = $this->detectValueType($value);
        $formatter = $this->selectFormatter($type);
        if ($formatter === null) {
            return parent::resolve($parameter, $value);
        }
        return new Value($formatter($value));
    }
}
