<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize\Resolve;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;

use function array_key_exists;
use function class_exists;
use function is_string;
use function Serendipity\Type\Cast\toArray;

class WhenRecursiveUseBuildChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Values $values): Value
    {
        $name = $this->name($parameter);
        $class = $this->resolveDependencyClass($parameter);
        if ($class === null) {
            return parent::resolve($parameter, $values);
        }
        $value = $values->get($name);
        if (is_object($value) && is_subclass_of($value::class, $class)) {
            return new Value($value);
        }
        $args = $this->resolveDependencyArgs($class, $value);
        if ($args === null) {
            return parent::resolve($parameter, $values);
        }
        return new Value($this->build($class, $args));
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    protected function parseParametersToValues(array $parameters, mixed $value): Values
    {
        $input = toArray($value, [$value]);
        $values = [];
        foreach ($parameters as $index => $parameter) {
            $name = $this->name($parameter);
            if (array_key_exists($name, $input) || array_key_exists($index, $input)) {
                $values[$name] = $input[$name] ?? $input[$index];
            }
        }
        return Values::createFrom($values);
    }

    /**
     * @return null|class-string<object>
     */
    private function resolveDependencyClass(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();
        $classes = $this->normalizeType($type);
        foreach ($classes as $class) {
            if (is_string($class) && class_exists($class)) {
                return $class;
            }
        }
        return null;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @throws ReflectionException
     */
    private function resolveDependencyArgs(string $class, mixed $value): ?Values
    {
        $reflectionClass = new ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return null;
        }
        $parameters = $constructor->getParameters();
        if (empty($parameters)) {
            return null;
        }
        return $this->parseParametersToValues($parameters, $value);
    }
}
