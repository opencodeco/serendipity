<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Exception\Adapter\NotResolvedCollection;
use Serendipity\Domain\Support\Value;

use function array_map;
use function gettype;
use function implode;
use function is_callable;
use function is_object;
use function Serendipity\Type\String\snakify;
use function sort;
use function sprintf;

abstract class Engine
{
    public function __construct(
        public readonly CaseConvention $case = CaseConvention::SNAKE,
        /**
         * @var array<callable|Formatter>
         */
        public readonly array $formatters = [],
        /**
         * @var array<string>
         */
        protected readonly array $path = [],
    ) {
    }

    protected function formatParameterName(ReflectionParameter|string $field): string
    {
        $name = is_string($field) ? $field : $field->getName();
        return match ($this->case) {
            CaseConvention::SNAKE => snakify($name),
            CaseConvention::NONE => $name,
        };
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
     * @param array<NotResolved>|string $unresolved
     */
    protected function notResolved(array|string $unresolved, mixed $value = null): Value
    {
        if (is_array($unresolved)) {
            return new Value(new NotResolvedCollection($unresolved, $this->path, $value));
        }
        $field = implode('.', $this->path);
        return new Value(new NotResolved($unresolved, $field, $value));
    }

    protected function notResolvedAsRequired(): Value
    {
        $field = implode('.', $this->path);
        $message = sprintf("The value for '%s' is required and was not given.", $field);
        return $this->notResolved($message);
    }

    protected function notResolvedAsInvalid(mixed $value): Value
    {
        $field = implode('.', $this->path);
        $message = sprintf("The value given for '%s' is not supported.", $field);
        return $this->notResolved($message, $value);
    }

    protected function notResolvedAsTypeMismatch(string $expected, string $actual, mixed $value): Value
    {
        $field = implode('.', $this->path);
        $message = sprintf(
            "The value for '%s' must be of type '%s' and '%s' was given.",
            $field,
            $expected,
            $actual,
        );
        return $this->notResolved($message, $value);
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
