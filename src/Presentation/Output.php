<?php

declare(strict_types=1);

namespace Serendipity\Presentation;

use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Values;

class Output implements Message
{
    private readonly Values $properties;

    private readonly ?Values $content;

    public function __construct(
        array $properties = [],
        ?array $content = null
    ) {
        $this->properties = Values::createFrom($properties);
        $this->content = $content === null ? null : Values::createFrom($content);
    }

    public static function createFrom(array $properties, ?array $content): self
    {
        return new self($properties, $content);
    }

    public function properties(): Values
    {
        return $this->properties;
    }

    public function property(string $key, mixed $default = null): mixed
    {
        return $this->properties->get($key, $default);
    }

    public function values(): ?Values
    {
        return $this->content;
    }

    public function value(string $key, mixed $default = null): mixed
    {
        return $this->values()?->get($key, $default);
    }
}
