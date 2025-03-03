<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionClass;
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
     *
     * @return T
     * @throws AdapterException
     */
    public function build(string $class, Set $set, array $path = []): mixed
    {
        try {
            $target = $this->extractTarget($class);
            if (empty($target->parameters)) {
                return new $class();
            }
            $parameters = $target->parameters;
            $args = $this->resolveArgs($parameters, $set, $path);
            return $target->reflection->newInstanceArgs($args);
        } catch (AdapterException $e) {
            throw $e;
        } catch (Throwable $error) {
            throw new AdapterException(values: $set, error: $error);
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return Target
     * @throws ReflectionException
     */
    public function extractTarget(string $class): Target
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        return new Target($reflection, $constructor?->getParameters() ?? []);
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    private function resolveArgs(array $parameters, Set $set, array $path): array
    {
        $coordinator = new Coordinator();
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

            $coordinator->compute($resolved);
        }

        if (empty($coordinator->errors())) {
            return $coordinator->args();
        }
        throw new AdapterException($set, $coordinator->errors());
    }
}
