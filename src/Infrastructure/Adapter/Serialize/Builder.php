<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Exception\AdapterException;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\BackedEnumValue;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\DependencyValue;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\FormatValue;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\NoValue;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\TypeMatched;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\ValidateValue;
use Throwable;

class Builder extends Engine
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string> $path
     *
     * @return T
     * @throws AdapterException
     */
    public function build(string $class, Set $set, array $path = []): mixed
    {
        try {
            return $this->make($class, $set, $path);
        } catch (AdapterException $error) {
            throw $error;
        } catch (Throwable $error) {
            throw new AdapterException(values: $set, error: $error);
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string> $path
     *
     * @return T
     * @throws ReflectionException
     * @throws AdapterException
     */
    protected function make(string $class, Set $set, array $path = []): mixed
    {
        $target = Target::createFrom($class);
        $parameters = $target->parameters();
        if (empty($parameters)) {
            /* @phpstan-ignore return.type */
            return $target->reflection()->newInstance();
        }

        $formula = new Formula();

        $this->resolveFormula($formula, $parameters, $set, $path);

        if (empty($formula->errors())) {
            /* @phpstan-ignore return.type */
            return $target->reflection()->newInstanceArgs($formula->args());
        }
        throw new AdapterException($set, $formula->errors());
    }

    /**
     * @param array<ReflectionParameter> $parameters
     * @param array<string> $path
     */
    private function resolveFormula(Formula $formula, array $parameters, Set $set, array $path): void
    {
        foreach ($parameters as $parameter) {
            $case = $this->case;
            $formatters = $this->formatters;
            $local = [...$path, $parameter->getName()];
            $resolved = (new ValidateValue(case: $case, path: $local))
                ->then(new DependencyValue(case: $case, path: $local))
                ->then(new BackedEnumValue(case: $case, path: $local))
                ->then(new FormatValue($case, $formatters, $local))
                ->then(new TypeMatched(case: $case, path: $local))
                ->then(new NoValue(case: $case, path: $local))
                ->resolve($parameter, $set);

            $formula->compute($resolved);
        }
    }
}
