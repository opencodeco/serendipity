<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

class ValidateValue extends TypeMatched
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $field = $this->casedName($parameter);
        if ($set->hasNot($field)) {
            return $this->notResolvedAsRequired();
        }

        $type = $parameter->getType();
        $value = $set->get($field);
        $resolved = $this->resolveReflectionParameterType($type, $value);
        return $resolved
            ?? $this->notResolvedAsTypeMismatch(
                $this->formatTypeName($type),
                $this->detectType($value),
                $value,
            );
    }
}
