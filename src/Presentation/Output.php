<?php

declare(strict_types=1);

namespace Serendipity\Presentation;

use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Set;

class Output implements Message
{
    private readonly Set $properties;

    private readonly ?Set $values;

    public function __construct(
        array $properties = [],
        ?array $values = null
    ) {
        $this->properties = Set::createFrom($properties);
        $this->values = $values === null ? null : Set::createFrom($values);
    }

    public static function createFrom(array $properties = [], ?array $values = null): self
    {
        return new self($properties, $values);
    }

    public function properties(): Set
    {
        return $this->properties;
    }

    public function property(string $key, mixed $default = null): mixed
    {
        return $this->properties->get($key, $default);
    }

    public function values(): ?Set
    {
        return $this->values;
    }

    public function value(string $key, mixed $default = null): mixed
    {
        return $this->values()?->get($key, $default);
    }
}
