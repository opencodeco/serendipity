<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective;

use ReflectionClass;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Collection\Collection;
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

    protected function selectFormatter(string $candidate): ?callable
    {
        $formatter = $this->formatters[$candidate] ?? null;
        if ($formatter !== null) {
            return $this->matchFormatter($formatter);
        }
        foreach ($this->formatters as $type => $formatter) {
            if (is_string($type) && class_exists($candidate) && is_subclass_of($candidate, $type)) {
                return $this->matchFormatter($formatter);
            }
        }
        return null;
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

    protected function detectCollectionName(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();
        if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }
        $candidate = $type->getName();
        return class_exists($candidate) && is_subclass_of($candidate, Collection::class)
            ? $candidate
            : null;
    }

    /**
     * @throws ReflectionException
     */
    protected function detectCollectionType(ReflectionClass $collection): ?string
    {
        $method = $collection->getMethod('current');
        $returnType = $method->getReturnType();
        if ($returnType && ! $returnType->isBuiltin()) {
            return $returnType instanceof ReflectionNamedType ? $returnType->getName() : null;
        }
        return null;
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

    private function matchFormatter(mixed $formatter): ?callable
    {
        return match (true) {
            $formatter instanceof Formatter => $this->formatFormatter($formatter),
            is_callable($formatter) => $formatter,
            default => null,
        };
    }

    private function formatFormatter(Formatter $formatter): callable
    {
        return fn (mixed $value, mixed $option = null): mixed => $formatter->format($value, $option);
    }
}
