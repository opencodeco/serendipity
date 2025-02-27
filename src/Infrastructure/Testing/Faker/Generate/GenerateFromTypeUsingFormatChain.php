<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Faker\Generate;

use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;
use Throwable;

final class GenerateFromTypeUsingFormatChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, ?Values $preset = null): ?Value
    {
        $type = $this->extractType($parameter);
        if ($type === null) {
            return parent::resolve($parameter, $preset);
        }
        try {
            return new Value($this->engine->format($type));
        } catch (Throwable) {
        }
        return parent::resolve($parameter, $preset);
    }
}
