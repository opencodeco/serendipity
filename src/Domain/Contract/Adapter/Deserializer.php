<?php

declare(strict_types=1);

namespace Serendipity\Domain\Contract\Adapter;

/**
 * @template T of object
 */
interface Deserializer
{
    /**
     * @param T $instance
     * @return array<string, mixed>
     */
    public function deserialize(mixed $instance): array;
}
