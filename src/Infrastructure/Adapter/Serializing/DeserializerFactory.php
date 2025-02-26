<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serializing;

use Serendipity\Domain\Contract\DeserializerFactory as Contract;

class DeserializerFactory implements Contract
{
    /**
     * @template T of object
     * @param class-string<T> $type
     * @return Deserializer<T>
     */
    public function make(string $type): Deserializer
    {
        return new Deserializer(type: $type, converters: $this->converters());
    }

    protected function converters(): array
    {
        return [];
    }
}
