<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionClass;
use ReflectionParameter;
use Serendipity\Domain\Exception\AdapterException;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\Consolidator;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\WhenCanConvertUseConverterChain;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\WhenEnumUseBackedChain;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\WhenIsValidUseValueChain;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\WhenNoValueUseDefaultChain;
use Serendipity\Infrastructure\Adapter\Serialize\Resolve\WhenRecursiveUseBuildChain;
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
            $resolved = (new WhenIsValidUseValueChain($this->case))
                ->then(new WhenRecursiveUseBuildChain($this->case))
                ->then(new WhenEnumUseBackedChain($this->case))
                ->then(new WhenCanConvertUseConverterChain($this->case, $this->converters))
                ->then(new WhenNoValueUseDefaultChain($this->case))
                ->resolve($parameter, $values);

            $consolidator->consolidate($resolved);
        }

        if (empty($consolidator->errors())) {
            return $consolidator->args();
        }
        throw new AdapterException($values, $consolidator->errors());
    }
}
