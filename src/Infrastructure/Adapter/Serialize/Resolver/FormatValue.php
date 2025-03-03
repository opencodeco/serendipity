<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\ResolverTyped;

class FormatValue extends ResolverTyped
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $type = $parameter->getType();
        if ($type === null) {
            return parent::resolve($parameter, $set);
        }

        $expected = $this->formatTypeName($type);
        $formatter = $this->selectFormatter($expected);
        if ($formatter === null) {
            return parent::resolve($parameter, $set);
        }

        $field = $this->casedName($parameter);
        $value = $set->get($field);

        $content = $formatter($value, $set);

        $resolved = $this->resolveReflectionParameterType($type, $content);
        return $resolved ??
            $this->notResolvedAsTypeMismatch($expected, $this->detectValueType($content), $content);
    }
}
