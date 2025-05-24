<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolver;

use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\ResolverTyped;

use function Serendipity\Type\Cast\stringify;

final class FormatValue extends ResolverTyped
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $type = $parameter->getType();
        if ($type === null) {
            return parent::resolve($parameter, $set);
        }

        $expected = stringify($this->formatTypeName($type));
        $formatter = $this->selectFormatter($expected);
        if ($formatter === null) {
            return parent::resolve($parameter, $set);
        }

        $field = $this->casedField($parameter);
        $value = $set->get($field);

        $formatted = $formatter($value, $expected);
        $resolved = $this->resolveReflectionParameterType($type, $formatted);
        if ($resolved) {
            return $resolved;
        }
        return parent::resolve($parameter, $set->with($field, $formatted));
    }
}
