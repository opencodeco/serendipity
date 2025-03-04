<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionParameter;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Exception\Adapter\NotResolvedCollection;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\CaseConvention;

abstract class Resolver extends Builder
{
    protected ?Resolver $previous = null;

    public function __construct(
        CaseConvention $case = CaseConvention::SNAKE,
        array $formatters = [],
        protected readonly array $path = [],
    ) {
        parent::__construct($case, $formatters);
    }

    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $set);
        }
        return $this->notResolvedAsInvalid($set->get($this->casedName($parameter)));
    }

    final public function then(Resolver $chain): Resolver
    {
        $chain->previous($this);
        return $chain;
    }

    protected function previous(Resolver $previous): void
    {
        $this->previous = $previous;
    }

    protected function notResolved(string|array $unresolved, mixed $value = null): Value
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
}
