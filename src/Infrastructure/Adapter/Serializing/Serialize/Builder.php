<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serializing\Serialize;

use ReflectionClass;
use ReflectionParameter;
use Serendipity\Domain\Exception\MappingException;
use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve\Consolidator;
use Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve\WhenCanConvertUseConverterChain;
use Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve\WhenEnumUseBackedChain;
use Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve\WhenIsValidUseValueChain;
use Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve\WhenNoValueUseDefaultChain;
use Serendipity\Infrastructure\Adapter\Serializing\Serialize\Resolve\WhenRecursiveUseBuildChain;
use Throwable;

class Builder extends Engine
{
    /**
     * @template T of object
     * @param class-string<T> $class
     *
     * @return T
     * @throws MappingException
     */
    public function build(string $class, Values $values): mixed
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
        } catch (MappingException $e) {
            throw $e;
        } catch (Throwable $error) {
            throw new MappingException(values: $values, error: $error);
        }
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    private function resolveArgs(array $parameters, Values $values): array
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
        throw new MappingException($values, $consolidator->errors());
    }
}
