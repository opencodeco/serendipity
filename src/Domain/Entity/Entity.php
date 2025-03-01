<?php

declare(strict_types=1);

namespace Serendipity\Domain\Entity;

use Serendipity\Domain\Support\Set;

class Entity
{
    public function expose(): Set
    {
        return Set::createFrom(get_object_vars($this));
    }
}
