<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Deserialize\Resolve;

use DateTimeInterface;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Type\Timestamp;
use Serendipity\Infrastructure\Adapter\Deserialize\Chain;

class DateChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $resolved = $this->resolveByClassName($value);
        if ($resolved) {
            return new Value($resolved);
        }
        return parent::resolve($parameter, $value);
    }

    private function resolveByClassName(mixed $value): ?string
    {
        return match (true) {
            $value instanceof Timestamp => $value->toString(),
            $value instanceof DateTimeInterface => $value->format(DateTimeInterface::ATOM),
            default => null,
        };
    }
}
