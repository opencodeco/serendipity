<?php

declare(strict_types=1);

namespace Serendipity\Domain\Entity;

use JsonSerializable;
use Serendipity\Domain\Contract\Exportable;

class Entity implements Exportable, JsonSerializable
{
    public function export(): array
    {
        return get_object_vars($this);
    }

    public function jsonSerialize(): array
    {
        return $this->export();
    }
}
