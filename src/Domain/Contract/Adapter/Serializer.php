<?php

declare(strict_types=1);

namespace Serendipity\Domain\Contract\Adapter;

/**
 * @template T of object
 */
interface Serializer
{
    /**
     * @param array<string, mixed> $datum
     * @return T
     */
    public function serialize(array $datum): mixed;
}
