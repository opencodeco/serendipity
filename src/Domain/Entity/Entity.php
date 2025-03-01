<?php

declare(strict_types=1);

namespace Serendipity\Domain\Entity;

use Serendipity\Domain\Support\Values;

class Entity
{
    public function expose(): Values
    {
        return Values::createFrom(get_object_vars($this));
    }
}
