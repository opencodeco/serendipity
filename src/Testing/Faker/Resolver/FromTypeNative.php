<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Resolver;

use DateMalformedStringException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Testing\Extension\ManagedExtension;
use Serendipity\Testing\Faker\Resolver;

final class FromTypeNative extends Resolver
{
    use MakeExtension;
    use ManagedExtension;

    /**
     * @throws DateMalformedStringException
     */
    public function resolve(ReflectionParameter $parameter, Set $preset): ?Value
    {
        $type = $this->extractType($parameter);
        if ($type === null) {
            return parent::resolve($parameter, $preset);
        }

        return $this->resolveByNative($type)
            ?? parent::resolve($parameter, $preset);
    }

    /**
     * @throws DateMalformedStringException
     */
    private function resolveByNative(string $type): ?Value
    {
        return match ($type) {
            DateTimeImmutable::class => new Value(new DateTimeImmutable($this->now())),
            DateTime::class => new Value(new DateTime($this->now())),
            default => null,
        };
    }

    private function now(): string
    {
        return $this->generator->dateTime()->format(DateTimeInterface::ATOM);
    }
}
