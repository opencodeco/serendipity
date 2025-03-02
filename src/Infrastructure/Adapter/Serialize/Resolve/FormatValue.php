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
        return $this->resolveTypeMatched($parameter, $set->with($field, $content));
    }

    private function select(?ReflectionType $type): ?callable
    {
        $name = $this->extract($type);
        return $this->formatter($name);
    }

    private function extract(?ReflectionType $type): string
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $type->getName(),
            $type instanceof ReflectionUnionType => $this->join($type->getTypes(), '|'),
            $type instanceof ReflectionIntersectionType => $this->join($type->getTypes(), '&'),
            default => 'undefined',
        };
    }

    /**
     * @param array<ReflectionType> $types
     * @param string $separator
     * @return string
     */
    private function join(array $types, string $separator): string
    {
        $array = array_map(fn (ReflectionType $type) => $this->extract($type), $types);
        sort($array);
        return implode($separator, $array);
    }
}
