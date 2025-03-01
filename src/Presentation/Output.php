<?php

declare(strict_types=1);

namespace Serendipity\Presentation;

use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Set;

class Output implements Message
{
    private readonly Set $properties;

    private readonly mixed $values;

    public function __construct(
        array $properties = [],
        ?array $values = null
    ) {
        $this->properties = Set::createFrom($properties);
        $this->values = $values === null ? null : Set::createFrom($values);
    }

    public function properties(): Set
    {
        return $this->properties;
    }

    public function property(string $key, mixed $default = null): mixed
    {
        return $this->properties->get($key, $default);
    }

    public function content(): mixed
    {
        return $this->values;
    }

    public function value(string $key, mixed $default = null): mixed
    {
        return $this->content()?->get($key, $default);
    }
}
