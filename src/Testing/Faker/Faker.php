<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use Faker\Factory;
use Faker\Generator as Engine;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\CaseConvention;
use Serendipity\Testing\Faker\Generate\GenerateFromDefaultValueChain;
use Serendipity\Testing\Faker\Generate\GenerateFromEnumChain;
use Serendipity\Testing\Faker\Generate\GenerateFromNameChain;
use Serendipity\Testing\Faker\Generate\GenerateFromPresetChain;
use Serendipity\Testing\Faker\Generate\GenerateFromTypeChain;

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
        return Set::createFrom($values);
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
