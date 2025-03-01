<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;

final class FromPresetChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, ?Set $preset = null): ?Value
    {
        if (! isset($preset)) {
            return parent::resolve($parameter, $preset);
        }
        $field = $this->normalizeName($parameter);
        if ($preset->has($field)) {
            return new Value($preset->get($field));
        }
        return parent::resolve($parameter, $preset);
    }
}
