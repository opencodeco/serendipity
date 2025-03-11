<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize\Resolve;

use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;

class FormatterChain extends Chain
{
    public function resolve(mixed $value): Value
    {
        $type = $this->detectValueType($value);
        $formatter = $this->selectFormatter($type);
        if ($formatter === null) {
            return parent::resolve($value);
        }
        return new Value($formatter($value));
    }
}
