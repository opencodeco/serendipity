<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

class FormatValue extends TypeMatched
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $type = $parameter->getType();
        if ($type === null) {
            return parent::resolve($parameter, $set);
        }
        $formatter = $this->select($type);
        if ($formatter === null) {
            return parent::resolve($parameter, $set);
        }
        $field = $this->name($parameter);
        $value = $set->get($field);
        $content = $formatter($value, $set);
        $new = $set->with($field, $content);
        return parent::resolve($parameter, $new);
    }

    private function select(?ReflectionType $type): ?callable
    {
        $name = $this->formatTypeName($type);
        return $name === null
            ? null
            : $this->formatter($name);
    }
}
