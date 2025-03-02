<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionClass;
use ReflectionParameter;
use Serendipity\Domain\Exception\AdapterException;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\Consolidator;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\UseBackedEnumValueChain;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\UseBuildChain;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\UseDefaultChain;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\UseTransformerChain;
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
            $reflectionClass = new ReflectionClass($class);
            $constructor = $reflectionClass->getConstructor();

            if ($constructor === null) {
                return new $class();
            }

            $parameters = $constructor->getParameters();
            $args = $this->resolveArgs($parameters, $values);
            return $reflectionClass->newInstanceArgs($args);
        } catch (AdapterException $e) {
            throw $e;
        } catch (Throwable $error) {
            throw new AdapterException(values: $values, error: $error);
        }
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    private function resolveArgs(array $parameters, Set $values): array
    {
        $consolidator = new Consolidator();
        foreach ($parameters as $parameter) {
            $resolved = (new UseValidatedValueChain($this->case))
                ->then(new UseBuildChain($this->case))
                ->then(new UseBackedEnumValueChain($this->case))
                ->then(new UseTransformerChain($this->case, $this->formatters))
                ->then(new UseDefaultChain($this->case))
                ->resolve($parameter, $values);

            $consolidator->consolidate($resolved);
        }

        if (empty($consolidator->errors())) {
            return $consolidator->args();
        }
        throw new AdapterException($values, $consolidator->errors());
    }
}
