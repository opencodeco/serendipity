<?php

declare(strict_types=1);

namespace Serendipity\Testing\Faker\Generate;

use ReflectionParameter;
use Serendipity\Domain\Exception\GeneratingException;
use Serendipity\Domain\Support\Value;
use Serendipity\Domain\Support\Set;
use Serendipity\Infrastructure\CaseConvention;
use Serendipity\Infrastructure\Repository\Generator;
use Throwable;

final class GenerateFromNameChain extends Chain
{
    private readonly Generator $generator;

    public function __construct(
        CaseConvention $case = CaseConvention::SNAKE,
        array $converters = [],
    ) {
        parent::__construct($case, $converters);

        $this->generator = $this->make(Generator::class);
    }

    /**
     * @throws GeneratingException
     */
    public function resolve(ReflectionParameter $parameter, ?Set $preset = null): ?Value
    {
        $name = $parameter->getName();
        try {
            return new Value($this->engine->format($name));
        } catch (Throwable) {
        }

        return match ($name) {
            'id' => new Value($this->generator->id()),
            'updatedAt', 'createdAt' => new Value($this->generator->now()),
            default => parent::resolve($parameter, $preset),
        };
    }
}
