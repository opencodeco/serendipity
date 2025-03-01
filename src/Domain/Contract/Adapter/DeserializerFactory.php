<?php

declare(strict_types=1);

namespace Serendipity\Domain\Contract\Adapter;

interface DeserializerFactory
{
    /**
     * @template T of object
     * @param class-string<T> $type
     * @return Deserializer<T>
     */
    public function make(string $type): Deserializer;
}
