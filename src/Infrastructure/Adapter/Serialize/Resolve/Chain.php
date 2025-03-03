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
        return $this->notResolvedAsInvalid($parameter, $set);
    }

    protected function previous(Chain $previous): void
    {
        $this->previous = $previous;
    }

    protected function notResolved(string $message, Set $set): Value
    {
        $field = implode('.', $this->path);
        $value = $set->has($field)
            ? new Value($set->get($field))
            : null;
        return new Value(new NotResolved($message, $field, $value));
    }

    protected function notResolvedAsRequired(ReflectionParameter $parameter, Set $set): Value
    {
        $message = sprintf("The value for '%s' is required and was not given.", $parameter->getName());
        return $this->notResolved($message, $set);
    }

    protected function notResolvedAsInvalid(ReflectionParameter $parameter, Set $set): Value
    {
        $message = sprintf("The value given for '%s' is invalid and can't be resolved.", $parameter->getName());
        return $this->notResolved($message, $set);
    }

    protected function notResolvedAsTypeMismatch(
        ReflectionParameter $parameter,
        Set $set,
        string $expected,
        string $actual
    ): Value {
        $message = sprintf(
            "The value for '%s' must be of type '%s' and '%s' was given.",
            $parameter->getName(),
            $expected,
            $actual,
        );
        return $this->notResolved($message, $set);
    }
}
