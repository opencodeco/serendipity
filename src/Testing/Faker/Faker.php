<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use Faker\Factory;
use Faker\Generator;
use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Support\Meta\Engine;
use Serendipity\Domain\Support\Meta\Target;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\CaseConvention;
use Serendipity\Testing\Faker\Resolver\FromDefaultValue;
use Serendipity\Testing\Faker\Resolver\FromDependency;
use Serendipity\Testing\Faker\Resolver\FromEnum;
use Serendipity\Testing\Faker\Resolver\FromPreset;
use Serendipity\Testing\Faker\Resolver\FromType;

class Faker extends Engine
{
    protected readonly Generator $generator;

    /**
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(
        CaseConvention $case = CaseConvention::SNAKE,
        array $converters = [],
    ) {
        parent::__construct($case, $converters);

        $this->generator = Factory::create('pt_BR');
    }

    /**
     * @template U of object
     * @param class-string<U> $class
     * @throws ReflectionException
     */
    public function fake(string $class, array $presets = []): Set
    {
        $target = Target::createFrom($class);
        $parameters = $target->getReflectionParameters();
        if (empty($parameters)) {
            return Set::createFrom([]);
        }

        return $this->resolveParameters($parameters, new Set($presets));
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->generator->__call($name, $arguments);
    }

    public function generator(): Generator
    {
        return $this->generator;
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    private function resolveParameters(array $parameters, Set $preset): Set
    {
        $values = [];
        foreach ($parameters as $parameter) {
            $field = $this->formatParameterName($parameter);
            $generated = (new FromDependency($this->case))
                ->then(new FromType($this->case))
                ->then(new FromEnum($this->case))
                ->then(new FromDefaultValue($this->case))
                ->then(new FromPreset($this->case))
                ->resolve($parameter, $preset);

            if ($generated === null) {
                continue;
            }
            $values[$field] = $generated->content;
        }
        return Set::createFrom($values);
    }
}
