<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective\Attributes;

use Attribute;
use Serendipity\Domain\Support\Reflective\Definition\Type;
use Serendipity\Domain\Support\Reflective\Definition\TypeExtended;

#[Attribute]
readonly class Define
{
    /**
     * @var array<Type|TypeExtended>
     */
    public array $types;

    public function __construct(Type|TypeExtended ...$type)
    {
        $this->types = $type;
    }
}
