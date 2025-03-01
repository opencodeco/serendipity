<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Generate;

use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Set;

final class GenerateFromPresetChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, ?Set $preset = null): ?Value
    {
        if (! isset($preset)) {
            return parent::resolve($parameter, $preset);
        }
        $field = $this->name($parameter);
        if ($preset->has($field)) {
            return new Value($preset->get($field));
        }
        return parent::resolve($parameter, $preset);
    }
}
