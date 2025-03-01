<?php

declare(strict_types=1);

namespace Serendipity\Domain\Entity;

use JsonSerializable;
use Serendipity\Domain\Contract\Exportable;

class Entity implements Exportable, JsonSerializable
{
    public function export(): object
    {
        return (object) get_object_vars($this);
    }

    public function jsonSerialize(): object
    {
        return $this->export();
    }
}
