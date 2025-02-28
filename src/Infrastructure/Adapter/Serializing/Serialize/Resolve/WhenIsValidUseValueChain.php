<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve;

use ReflectionParameter;
use Serendipity\Domain\Exception\Mapping\NotResolvedType;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;

class WhenIsValidUseValueChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, Values $values): Value
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
        return $this->notResolved(NotResolvedType::INVALID, $parameter, $values);
    }

    private function isValidType(mixed $value, string $expected): bool
    {
        $type = $this->type($value);
        return $type === $expected || ($type === 'object' && $value instanceof $expected);
    }
}
