<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Faker\Generate;

use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Throwable;

final class NamedChain extends Chain
{
    public function resolve(ReflectionParameter $parameter): ?Value
    {
        $name = $parameter->getName();
        try {
            return new Value($this->faker->format($name));
        } catch (Throwable) {
        }
        return parent::resolve($parameter);
    }
}
