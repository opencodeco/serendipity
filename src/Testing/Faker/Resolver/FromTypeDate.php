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
use Serendipity\Domain\Type\Timestamp;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Testing\Extension\ManagedExtension;
use Serendipity\Testing\Faker\Resolver;

final class FromTypeDate extends Resolver
{
    use MakeExtension;
    use ManagedExtension;

    /**
     * @throws DateMalformedStringException
     */
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $type = $this->detectReflectionType($parameter->getType());
        if ($type === null) {
            return parent::resolve($parameter, $presets);
        }

        return $this->resolveByClassName($type)
            ?? parent::resolve($parameter, $presets);
    }

    /**
     * @throws DateMalformedStringException
     */
    private function resolveByClassName(string $type): ?Value
    {
        $now = $this->now();
        return match ($type) {
            Timestamp::class => new Value(new Timestamp($now)),
            DateTimeImmutable::class => new Value(new DateTimeImmutable($now)),
            DateTime::class,
            DateTimeInterface::class => new Value(new DateTime($now)),
            default => null,
        };
    }

    private function now(): string
    {
        return $this->generator->dateTime()->format(DateTimeInterface::ATOM);
    }
}
