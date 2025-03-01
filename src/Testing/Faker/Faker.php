<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\CaseConvention;

use function Serendipity\Type\String\toSnakeCase;

/**
 * @template T
 * @extends Generator<T>
 *
 * @phpstan-ignore generics.noParent
 */
class Faker
{
    protected readonly Generator $engine;

    /**
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(
        public readonly CaseConvention $case = CaseConvention::SNAKE,
        public readonly array $converters = [],
    ) {
        $this->engine = Factory::create('pt_BR');
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->engine->__call($name, $arguments);
    }

    /**
     * @template U of object
     * @param class-string<U> $class
     * @throws ReflectionException
     */
    public function fake(string $class, array $presets = []): Set
    {
        $preset = new Set($presets);
        $constructor = (new ReflectionClass($class))->getConstructor();
        if ($constructor === null) {
            return Set::createFrom([]);
        }

        return $this->parseValues($constructor, $preset);
    }

    public function parseValues(ReflectionMethod $constructor, Set $preset): Set
    {
        $values = [];
        foreach ($constructor->getParameters() as $parameter) {
            $field = $this->normalizeName($parameter);
            $generated = (new FromTypeChain($this->case))
                ->then(new FromNameChain($this->case))
                ->then(new FromEnumChain($this->case))
                ->then(new FromDefaultValueChain($this->case))
                ->then(new FromPresetChain($this->case))
                ->resolve($parameter, $preset);

            if ($generated === null) {
                continue;
            }
            $values[$field] = $generated->content;
        }
        return Set::createFrom($values);
    }

    protected function normalizeName(ReflectionParameter $parameter): string
    {
        $name = $parameter->getName();
        return match ($this->case) {
            CaseConvention::SNAKE => toSnakeCase($name),
            CaseConvention::NONE => $name,
        };
    }
}
