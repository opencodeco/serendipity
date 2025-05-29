<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support;

use JsonSerializable;
use Serendipity\Domain\Contract\Exportable;
use Throwable;

class Datum implements Exportable, JsonSerializable
{
    public function __construct(
        private readonly array $value,
        private readonly Throwable $throwable
    ) {
    }

    public function export(): object
    {
        # test 4
        return (object) [
            ...$this->value,
            '@error' => [
                'message' => $this->throwable->getMessage(),
                'code' => $this->throwable->getCode(),
                'file' => $this->throwable->getFile(),
                'line' => $this->throwable->getLine(),
            ],
        ];
    }

    public function jsonSerialize(): object
    {
        return $this->export();
    }
}
