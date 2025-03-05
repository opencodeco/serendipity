<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\ResolverTyped;

use function Serendipity\Type\Cast\toString;

class ValidateValue extends ResolverTyped
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $field = $this->formatParameterName($parameter);
        if ($set->hasNot($field)) {
            return $this->notResolvedAsRequired();
        }

        $type = $parameter->getType();
        $value = $set->get($field);
        $resolved = $this->resolveReflectionParameterType($type, $value);
        return $resolved
            ?? $this->notResolvedAsTypeMismatch(
                toString($this->formatTypeName($type)),
                $this->detectValueType($value),
                $value,
            );
    }
}
