<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use Faker\Factory;
use Faker\Generator;
use ReflectionException;
use ReflectionParameter;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Domain\Contract\Testing\Faker as Contract;
use Serendipity\Domain\Support\Reflective\CaseNotation;
use Serendipity\Domain\Support\Reflective\Engine;
use Serendipity\Domain\Support\Reflective\Factory\Target;
use Serendipity\Domain\Support\Set;
use Serendipity\Testing\Faker\Resolver\FromDefaultValue;
use Serendipity\Testing\Faker\Resolver\FromDependency;
use Serendipity\Testing\Faker\Resolver\FromEnum;
use Serendipity\Testing\Faker\Resolver\FromPreset;
use Serendipity\Testing\Faker\Resolver\FromTypeAttributes;
use Serendipity\Testing\Faker\Resolver\FromTypeBuiltin;
use Serendipity\Testing\Faker\Resolver\FromTypeNative;

use function Serendipity\Type\Cast\stringify;

class Faker extends Engine implements Contract
{
    protected readonly Generator $generator;

    /**
     * @param array<callable|Formatter> $formatters
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(
        CaseNotation $case = CaseNotation::SNAKE,
        array $formatters = [],
        ?string $locale = null,
    ) {
        parent::__construct($case, $formatters);

        $this->generator = Factory::create($locale ?? stringify(getenv('FAKER_LOCALE'), 'en_US'));
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->generate($name, $arguments);
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

    public function generate(string $name, array $arguments = []): mixed
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
    private function resolveParameters(array $parameters, Set $presets): Set
    {
        $values = [];
        foreach ($parameters as $parameter) {
            $field = $this->casedField($parameter);
            $generated = (new FromDependency($this->case))
                ->then(new FromTypeNative($this->case))
                ->then(new FromTypeBuiltin($this->case))
                ->then(new FromTypeAttributes($this->case))
                ->then(new FromEnum($this->case))
                ->then(new FromDefaultValue($this->case))
                ->then(new FromPreset($this->case))
                ->resolve($parameter, $presets);

            if ($generated === null) {
                continue;
            }
            $values[$field] = $generated->content;
        }
        return Set::createFrom($values);
    }
}
