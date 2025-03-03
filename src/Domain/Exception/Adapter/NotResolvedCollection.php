<?php

declare(strict_types=1);

namespace Serendipity\Domain\Exception\Adapter;

final readonly class NotResolvedCollection
{
    public function __construct(
        public array $notResolved,
        public array $path,
        public mixed $value = null,
    ) {
    }
}
