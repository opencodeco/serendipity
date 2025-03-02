<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Support\Value;

class Coordinator
{
    /**
     * @var array mixed[]
     */
    private array $args = [];

    /**
     * @var NotResolved[]
     */
    private array $errors = [];

    public function compute(Value $value): void
    {
        if ($value->content instanceof NotResolved) {
            $this->errors[] = $value->content;
            return;
        }
        $this->args[] = $value->content;
    }

    /**
     * @return array mixed[]
     */
    public function args(): array
    {
        return $this->args;
    }

    /**
     * @return NotResolved[]
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
