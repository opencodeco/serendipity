<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective\Type;

use Attribute;

#[Attribute]
readonly class Text
{
    public function __construct(
        public string $pattern = '/.*/',
    ) {
    }
}
