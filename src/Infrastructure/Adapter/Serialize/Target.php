<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

/**
 * @template T of object
 */
final readonly class Target
{
    public function __construct(
        /**
         * @var ReflectionClass<T> $reflection
         */
        private ReflectionClass $reflection,
        /**
         * @var array<ReflectionParameter> $parameters
         */
        private array $parameters = [],
    ) {
    }

    /**
     * @param class-string<T> $class
     * @throws ReflectionException
     */
    public static function createFrom(string $class): Target
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        return new Target($reflection, $constructor?->getParameters() ?? []);
    }

    /**
     * @return ReflectionClass<T>
     */
    public function reflection(): ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @return array<ReflectionParameter>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }
}
