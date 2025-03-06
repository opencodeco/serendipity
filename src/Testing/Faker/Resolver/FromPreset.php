<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Resolver;

use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Testing\Faker\Resolver;

final class FromPreset extends Resolver
{
    public function resolve(ReflectionParameter $parameter, Set $preset): ?Value
    {
        $field = $this->formatParameterName($parameter);
        if ($preset->has($field)) {
            return new Value($preset->get($field));
        }
        return parent::resolve($parameter, $preset);
    }
}
