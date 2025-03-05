<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\ResolverTyped;

class TypeMatched extends ResolverTyped
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $field = $this->formatParameterName($parameter);
        if (! $set->has($field)) {
            return parent::resolve($parameter, $set);
        }
        return $this->resolveTypeMatched($parameter, $set, $field);
    }

    final protected function resolveTypeMatched(ReflectionParameter $parameter, Set $set, string $field): Value
    {
        $type = $parameter->getType();
        $value = $set->get($field);
        $resolved = $this->resolveReflectionParameterType($type, $value);
        return $resolved ?? parent::resolve($parameter, $set);
    }
}
