<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize\Resolve;

use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;

use function is_object;
use function Serendipity\Type\Cast\stringify;

class FormatterChain extends Chain
{
    public function resolve(mixed $value): Value
    {
        $type = $this->extractType($value);
        $formatter = $this->selectFormatter($type);
        if ($formatter === null) {
            return parent::resolve($value);
        }
        return new Value($formatter($value));
    }

    private function extractType(mixed $value): string
    {
        $type = $this->detectValueType($value);
        if ($type === 'object' && is_object($value)) {
            $type = $value::class;
        }
        return stringify($type);
    }
}
