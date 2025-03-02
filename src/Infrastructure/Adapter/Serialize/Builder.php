<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Exception\AdapterException;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\BackedEnumValueChain;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\FormatValue;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\NoValue;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\TypeMatched;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\UseBuildChain;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\UseValidatedValueChain;
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
    public function build(string $class, Set $values): mixed
    {
        try {
            $target = $this->target($class);
            if (empty($target->parameters)) {
                return new $class();
            }
            $parameters = $target->parameters;
            $args = $this->resolveArgs($parameters, $values);
            return $target->reflection->newInstanceArgs($args);
        } catch (AdapterException $e) {
            throw $e;
        } catch (Throwable $error) {
            throw new AdapterException(values: $values, error: $error);
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return Target
     * @throws ReflectionException
     */
    public function target(string $class): Target
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        return new Target($reflection, $constructor?->getParameters() ?? []);
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    private function resolveArgs(array $parameters, Set $values): array
    {
        $coordinator = new Coordinator();
        foreach ($parameters as $parameter) {
            $resolved = (new UseValidatedValueChain($this->case))
                ->then(new UseBuildChain($this->case))
                ->then(new BackedEnumValueChain($this->case))
                ->then(new FormatValue($this->case, $this->formatters))
                ->then(new TypeMatched($this->case))
                ->then(new NoValue($this->case))
                ->resolve($parameter, $values);

            $coordinator->compute($resolved);
        }

        if (empty($coordinator->errors())) {
            return $coordinator->args();
        }
        throw new AdapterException($values, $coordinator->errors());
    }
}
