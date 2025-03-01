<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize\Resolve;

use BackedEnum;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;

use function is_object;

class DependencyChain extends Chain
{
    public function resolve(mixed $value): Value
    {
        if (! is_object($value)) {
            return parent::resolve($value);
        }
        if ($this->isNotEnum($value::class)) {
            return new Value($this->demolish($value));
        }
        return new Value($value->value);
    }

    private function isNotEnum(string $enum): bool
    {
        return ! is_subclass_of($enum, BackedEnum::class) || ! enum_exists($enum);
    }
}
