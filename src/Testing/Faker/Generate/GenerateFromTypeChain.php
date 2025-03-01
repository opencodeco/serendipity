<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Generate;

use DateMalformedStringException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionParameter;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Set;
use Throwable;

final class GenerateFromTypeChain extends Chain
{
    /**
     * @throws DateMalformedStringException
     */
    public function resolve(ReflectionParameter $parameter, ?Set $preset = null): ?Value
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
            DateTimeImmutable::class => new Value(new DateTimeImmutable($this->now())),
            DateTime::class => new Value(new DateTime($this->now())),
            default => parent::resolve($parameter, $preset),
        };
    }

    private function now(): string
    {
        return $this->engine->dateTime()->format(DateTimeInterface::ATOM);
    }
}
