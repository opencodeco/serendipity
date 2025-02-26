<?php

declare(strict_types=1);

namespace Serendipity\Domain\Contract;

use Serendipity\Infrastructure\Adapter\Serializing\Deserializer;

interface DeserializerFactory
{
    /**
     * @template T of object
     * @param class-string<T> $type
     * @return Deserializer<T>
     */
    public function make(string $type): Deserializer;
}
