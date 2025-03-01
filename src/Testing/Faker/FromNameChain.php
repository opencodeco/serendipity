<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker;

use ReflectionParameter;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Domain\Support\Set;
use Serendipity\Domain\Support\Value;
use Serendipity\Infrastructure\CaseConvention;
use Serendipity\Infrastructure\Database\Instrument;
use Throwable;

final class FromNameChain extends Chain
{
    private readonly Instrument $instrument;

    public function __construct(
        CaseConvention $case = CaseConvention::SNAKE,
        array $converters = [],
    ) {
        parent::__construct($case, $converters);

        $this->instrument = $this->make(Instrument::class);
    }

    /**
     * @throws GeneratingException
     */
    public function resolve(ReflectionParameter $parameter, ?Set $preset = null): ?Value
    {
        $name = $parameter->getName();
        try {
            return new Value($this->instrument->format($name));
        } catch (Throwable) {
        }

        return match ($name) {
            'id' => new Value($this->instrument->id()),
            'updatedAt', 'createdAt' => new Value($this->instrument->now()),
            default => parent::resolve($parameter, $preset),
        };
    }
}
