<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Infrastructure\CaseConvention;

use function array_map;
use function gettype;
use function is_string;
use function Serendipity\Type\String\toSnakeCase;

abstract class Engine
{
    public function __construct(
        public readonly CaseConvention $case = CaseConvention::SNAKE,
        public readonly array $formatters = [],
    ) {
    }

    /**
     * @return array<class-string<object>|string>
     */
    protected function normalizeType(?ReflectionType $type): array
    {
        if ($type instanceof ReflectionNamedType) {
            return [$type->getName()];
        }
        if ($type instanceof ReflectionIntersectionType || $type instanceof ReflectionUnionType) {
            /** @var array<ReflectionNamedType> $reflectionNamedTypes */
            $reflectionNamedTypes = $type->getTypes();
            return array_map(
                fn (ReflectionIntersectionType|ReflectionNamedType $type) => $type->getName(),
                $reflectionNamedTypes
            );
        }
        return [];
    }

    protected function formatter(string $type): ?callable
    {
        $formatter = $this->formatters[$type] ?? null;
        return match (true) {
            $formatter instanceof Formatter => fn (mixed $value, mixed $option = null): mixed => $formatter->format(
                $value,
                $option
            ),
            is_callable($formatter) => $formatter,
            default => null,
        };
    }

    protected function name(ReflectionParameter|string $field): string
    {
        $name = is_string($field) ? $field : $field->getName();
        return match ($this->case) {
            CaseConvention::SNAKE => toSnakeCase($name),
            CaseConvention::NONE => $name,
        };
    }

    protected function detectType(mixed $value): string
    {
        $type = gettype($value);
        $type = match ($type) {
            'double' => 'float',
            'integer' => 'int',
            'boolean' => 'bool',
            'NULL' => 'null',
            default => $type,
        };
        if ($type === 'object') {
            return $value::class;
        }
        return $type;
    }

    protected function formatTypeName(?ReflectionType $type): ?string
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $type->getName(),
            $type instanceof ReflectionUnionType => $this->joinReflectionTypeNames($type->getTypes(), '|'),
            $type instanceof ReflectionIntersectionType => $this->joinReflectionTypeNames($type->getTypes(), '&'),
            default => null,
        };
    }

    /**
     * @param array<ReflectionType> $types
     * @param string $separator
     * @return string
     */
    private function joinReflectionTypeNames(array $types, string $separator): string
    {
        $array = array_map(fn (ReflectionType $type) => $this->formatTypeName($type), $types);
        sort($array);
        return implode($separator, $array);
    }
}
