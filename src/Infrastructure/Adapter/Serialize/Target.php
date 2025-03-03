<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionClass;
use ReflectionException;

final class Target
{
    public function __construct(
        public readonly ReflectionClass $reflection,
        public readonly array $parameters = [],
    ) {
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return Target
     * @throws ReflectionException
     */
    public static function createFrom(string $class): Target
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        return new Target($reflection, $constructor?->getParameters() ?? []);
    }
}
