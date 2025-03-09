<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Contract\Message;
use Serendipity\Presentation\Output;

abstract class Success extends Output
{
    final public function __construct(mixed $content, array $properties = [])
    {
        parent::__construct($content, $properties);
    }

    final public static function createFrom(mixed $content = null, array $properties = []): static
    {
        if ($content instanceof Message) {
            return new static(
                $content->content(),
                array_merge($content->properties()->toArray(), $properties)
            );
        }
        if ($content instanceof Exportable) {
            return new static($content->export(), $properties);
        }
        return new static($content, $properties);
    }
}
