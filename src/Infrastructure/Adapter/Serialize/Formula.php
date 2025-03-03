<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter\Serialize;

use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Exception\Adapter\NotResolvedCollection;
use Serendipity\Domain\Support\Value;

class Formula
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
        if ($value->content instanceof NotResolvedCollection) {
            $this->errors = array_merge($this->errors, $value->content->notResolved);
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
