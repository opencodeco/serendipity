<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Exception\AdapterException;
use Serendipity\Domain\Support\Reflective\Engine;
use Serendipity\Domain\Support\Reflective\Factory\Target;
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
        $parameters = $target->getReflectionParameters();
        if (empty($parameters)) {
            /* @phpstan-ignore return.type */
            return $target->getReflectionClass()->newInstance();
        }

        $resolution = new Resolution();

        $this->resolveParameters($resolution, $parameters, $set, $path);

        if (empty($resolution->errors())) {
            /* @phpstan-ignore return.type */
            return $target->getReflectionClass()->newInstanceArgs($resolution->args());
        }
        throw new AdapterException($set, $resolution->errors());
    }

    /**
     * @param array<ReflectionParameter> $parameters
     * @param array<string> $path
     */
    private function resolveParameters(Resolution $resolution, array $parameters, Set $set, array $path): void
    {
        foreach ($parameters as $parameter) {
            $nestedPath = [...$path, $parameter->getName()];
            $resolved = (new ValidateValue(case: $this->case, path: $nestedPath))
                ->then(new DependencyValue(case: $this->case, path: $nestedPath))
                ->then(new BackedEnumValue(case: $this->case, path: $nestedPath))
                ->then(new FormatValue($this->case, $this->formatters, $nestedPath))
                ->then(new TypeMatched(case: $this->case, path: $nestedPath))
                ->then(new NoValue(case: $this->case, path: $nestedPath))
                ->resolve($parameter, $set);

            $resolution->add($resolved);
        }
    }
}
