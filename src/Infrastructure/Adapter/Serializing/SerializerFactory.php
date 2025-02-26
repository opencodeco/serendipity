<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serializing;

use Serendipity\Domain\Contract\SerializerFactory as Contract;

class SerializerFactory implements Contract
{
    /**
     * @template T of object
     * @param class-string<T> $type
     * @return Serializer<T>
     */
    public function make(string $type): Serializer
    {
        return new Serializer(type: $type, converters: $this->converters());
    }

    protected function converters(): array
    {
        return [];
    }
}
