<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Domain\Support\Reflective\Engine\Resolution;

use function array_map;
use function gettype;
use function implode;
use function is_callable;
use function is_object;
use function sort;

abstract class Engine extends Resolution
{
    protected function formatTypeName(?ReflectionType $type): ?string
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $type->getName(),
            $type instanceof ReflectionUnionType => $this->joinReflectionTypeNames($type->getTypes(), '|'),
            $type instanceof ReflectionIntersectionType => $this->joinReflectionTypeNames($type->getTypes(), '&'),
            default => null,
        };
    }

    protected function selectFormatter(string $type): ?callable
    {
        $formatter = $this->formatters[$type] ?? null;
        return match (true) {
            $formatter instanceof Formatter => $this->formatFormatter($formatter),
            is_callable($formatter) => $formatter,
            default => null,
        };
    }

    protected function detectValueType(mixed $value): string
    {
        $type = gettype($value);
        $type = match ($type) {
            'double' => 'float',
            'integer' => 'int',
            'boolean' => 'bool',
            'NULL' => 'null',
            default => $type,
        };
        if ($type === 'object' && is_object($value)) {
            return $value::class;
        }
        return $type;
    }

    /**
     * @param array<ReflectionType> $types
     */
    private function joinReflectionTypeNames(array $types, string $separator): string
    {
        $array = array_map(fn (ReflectionType $type) => $this->formatTypeName($type), $types);
        sort($array);
        return implode($separator, $array);
    }

    private function formatFormatter(Formatter $formatter): callable
    {
        return fn (mixed $value, mixed $option = null): mixed => $formatter->format($value, $option);
    }
}
