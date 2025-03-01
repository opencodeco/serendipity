<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use DateMalformedStringException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Throwable;

final class FromTypeChain extends Chain
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
            return new Value($this->generator->format($type));
        } catch (Throwable) {
        }
        return match ($type) {
            'int' => new Value($this->generator->numberBetween(1, 100)),
            'string' => new Value($this->generator->word()),
            'bool' => new Value($this->generator->boolean()),
            'float' => new Value($this->generator->randomFloat(2, 1, 100)),
            'array' => new Value($this->generator->words()),
            DateTimeImmutable::class => new Value(new DateTimeImmutable($this->now())),
            DateTime::class => new Value(new DateTime($this->now())),
            default => parent::resolve($parameter, $preset),
        };
    }

    private function now(): string
    {
        return $this->generator->dateTime()->format(DateTimeInterface::ATOM);
    }
}
