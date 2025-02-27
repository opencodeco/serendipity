<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Faker;

use Faker\Factory;
use Faker\Generator as Engine;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\CaseConvention;
use Serendipity\Infrastructure\Testing\Faker\Generate\NamedChain;
use Serendipity\Infrastructure\Testing\Faker\Generate\OptionalChain;
use Serendipity\Infrastructure\Testing\Faker\Generate\TypedChain;
use Serendipity\Infrastructure\Testing\Faker\Provider\PersistenceProvider;

use function Serendipity\Type\String\toSnakeCase;

readonly class Faker
{
    public Engine $engine;

    /**
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(
        public PersistenceProvider $persistenceProvider,
        private CaseConvention $case = CaseConvention::SNAKE,
    ) {
        $this->engine = Factory::create('pt_BR');
        $this->engine->addProvider($persistenceProvider);
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
            $field = $this->normalize($parameter);
            if ($preset->has($field)) {
                $values[$field] = $preset->get($field);
                continue;
            }
            $generated = $this->generateValue($parameter);
            if ($generated === null) {
                continue;
            }
            $values[$field] = $generated->content;
        }
        return Values::createFrom($values);
    }

    private function normalize(ReflectionParameter $parameter): string
    {
        $name = $parameter->getName();
        return match ($this->case) {
            CaseConvention::SNAKE => toSnakeCase($name),
            CaseConvention::NONE => $name,
        };
    }

    private function generateValue(ReflectionParameter $parameter): ?Value
    {
        return (new TypedChain($this->engine))
            ->then(new NamedChain($this->engine))
            ->then(new OptionalChain($this->engine))
            ->resolve($parameter);
    }
}
