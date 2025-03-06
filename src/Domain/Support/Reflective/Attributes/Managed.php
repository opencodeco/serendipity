<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective\Attributes;

use Attribute;

#[Attribute]
readonly class Managed
{
    public function __construct(public string $management)
    {
    }
}
