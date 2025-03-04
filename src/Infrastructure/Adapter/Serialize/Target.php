<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionClass;
use ReflectionException;

final class Target
{
    public function __construct(
        private readonly ReflectionClass $reflection,
        private readonly array $parameters = [],
    ) {
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @throws ReflectionException
     */
    public static function createFrom(string $class): Target
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        return new Target($reflection, $constructor?->getParameters() ?? []);
    }

    public function reflection(): ReflectionClass
    {
        return $this->reflection;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }
}
