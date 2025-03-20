<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective\Factory;

use ReflectionException;
use Serendipity\Domain\Support\Reflective\Engine;
use Serendipity\Domain\Support\Reflective\Factory\Rules\AttributeChain;
use Serendipity\Domain\Support\Reflective\Factory\Rules\MandatoryChain;
use Serendipity\Domain\Support\Reflective\Factory\Rules\RecursiveChain;
use Serendipity\Domain\Support\Reflective\Factory\Rules\TypeChain;

class Ruler extends Engine
{
    /**
     * @template U of object
     * @param class-string<U> $class
     * @param array<string> $path
     *
     * @throws ReflectionException
     */
    public function ruleset(string $class, array $path = []): Ruleset
    {
        return $this->make($class, $path);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string> $path
     *
     * @throws ReflectionException
     */
    protected function make(string $class, array $path = [], Ruleset $ruleset = new Ruleset()): Ruleset
    {
        $target = Target::createFrom($class);
        $parameters = $target->getReflectionParameters();

        foreach ($parameters as $parameter) {
            $nestedPath = [...$path, $parameter->getName()];
            (new RecursiveChain(case: $this->case, path: $nestedPath))
                ->then(new AttributeChain(case: $this->case, path: $nestedPath))
                ->then(new TypeChain(case: $this->case, path: $nestedPath))
                ->then(new MandatoryChain(case: $this->case, path: $nestedPath))
                ->resolve($parameter, $ruleset);
        }
        return $ruleset;
    }
}
