<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionParameter;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Exception\Adapter\Type;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\Adapter\Serialize\Builder;
use Serendipity\Infrastructure\CaseConvention;

abstract class Chain extends Builder
{
    protected ?Chain $previous = null;

    public function __construct(
        CaseConvention $case = CaseConvention::SNAKE,
        array $formatters = [],
        protected readonly array $path = [],
    ) {
        parent::__construct($case, $formatters);
    }

    final public function then(Chain $chain): Chain
    {
        $chain->previous($this);
        return $chain;
    }

    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $set);
        }
        return $this->notResolvedAsInvalid($set->get($this->casedName($parameter)));
    }

    protected function previous(Chain $previous): void
    {
        $this->previous = $previous;
    }

    protected function notResolved(string $message, mixed $value = null): Value
    {
        $field = implode('.', $this->path);
        return new Value(new NotResolved($message, $field, $value));
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
        $message = sprintf("The value given for '%s' is invalid and can't be resolved.", $field);
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
