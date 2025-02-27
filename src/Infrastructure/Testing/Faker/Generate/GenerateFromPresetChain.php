<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Faker\Generate;

use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;

final class GenerateFromPresetChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, ?Values $preset = null): ?Value
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
