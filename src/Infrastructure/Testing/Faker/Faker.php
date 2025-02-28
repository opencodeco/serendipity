<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Faker;

use Faker\Factory;
use Faker\Generator as Engine;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\CaseConvention;
use Serendipity\Infrastructure\Testing\Faker\Generate\GenerateFromDefaultValueChain;
use Serendipity\Infrastructure\Testing\Faker\Generate\GenerateFromEnumChain;
use Serendipity\Infrastructure\Testing\Faker\Generate\GenerateFromNameChain;
use Serendipity\Infrastructure\Testing\Faker\Generate\GenerateFromPresetChain;
use Serendipity\Infrastructure\Testing\Faker\Generate\GenerateFromTypeChain;

use function Serendipity\Type\String\toSnakeCase;

class Faker
{
    public readonly Engine $engine;

    /**
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(
        public readonly CaseConvention $case = CaseConvention::SNAKE,
        public readonly array $converters = [],
    ) {
        $this->engine = Factory::create('pt_BR');
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @throws ReflectionException
     */
    public function fake(string $class, array $presets = []): Values
    {
        $preset = new Values($presets);
        $constructor = (new ReflectionClass($class))->getConstructor();
        if ($constructor === null) {
            return Values::createFrom([]);
        }

        return $this->parseValues($constructor, $preset);
    }

    public function parseValues(ReflectionMethod $constructor, Values $preset): Values
    {
        $values = [];
        foreach ($constructor->getParameters() as $parameter) {
            $field = $this->name($parameter);
            $generated = (new GenerateFromTypeChain($this->case))
                ->then(new GenerateFromNameChain($this->case))
                ->then(new GenerateFromEnumChain($this->case))
                ->then(new GenerateFromDefaultValueChain($this->case))
                ->then(new GenerateFromPresetChain($this->case))
                ->resolve($parameter, $preset);

            if ($generated === null) {
                continue;
            }
            $values[$field] = $generated->content;
        }
        return Values::createFrom($values);
    }

    protected function name(ReflectionParameter $parameter): string
    {
        $name = $parameter->getName();
        return match ($this->case) {
            CaseConvention::SNAKE => toSnakeCase($name),
            CaseConvention::NONE => $name,
        };
    }
}
