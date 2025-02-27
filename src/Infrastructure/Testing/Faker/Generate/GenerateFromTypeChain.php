<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing\Faker\Generate;

use DateTime;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Values;
use Throwable;

final class GenerateFromTypeChain extends Chain
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
        return match ($type) {
            'int' => new Value($this->engine->numberBetween(1, 100)),
            'string' => new Value($this->engine->word()),
            'bool' => new Value($this->engine->boolean()),
            'float' => new Value($this->engine->randomFloat(2, 1, 100)),
            'array' => new Value($this->engine->words()),
            \DateTimeImmutable::class, DateTime::class => new Value($this->engine->dateTime()),
            default => parent::resolve($parameter, $preset),
        };
    }
}
