<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionParameter;
use Serendipity\Domain\Exception\Adapter\Type;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

class UseValidatedValueChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, Set $values): Value
    {
        $type = $parameter->getType();
        $name = $this->name($parameter);

        $value = $values->get($name);
        if ($type === null) {
            return new Value($value);
        }

        $types = $this->normalizeType($type);
        foreach ($types as $type) {
            if ($this->isValidType($value, $type)) {
                return new Value($value);
            }
        }
        return $this->notResolved(Type::INVALID, $parameter, $values);
    }

    private function isValidType(mixed $value, string $expected): bool
    {
        $type = $this->type($value);
        return $type === $expected || ($type === 'object' && $value instanceof $expected);
    }
}
