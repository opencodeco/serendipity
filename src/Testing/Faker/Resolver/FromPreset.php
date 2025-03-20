<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Resolver;

use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Testing\Faker\Resolver;

final class FromPreset extends Resolver
{
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $field = $this->casedField($parameter);
        if ($presets->has($field)) {
            return new Value($presets->get($field));
        }
        return parent::resolve($parameter, $presets);
    }
}
