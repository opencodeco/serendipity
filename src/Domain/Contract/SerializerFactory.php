<?php

declare(strict_types=1);

namespace Serendipity\Domain\Contract;

use Serendipity\Infrastructure\Adapter\Serializing\Serializer;

interface SerializerFactory
{
    /**
     * @template T of object
     * @param class-string<T> $type
     * @return Serializer<T>
     */
    public function make(string $type): Serializer;
}
