<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Contract\Message;
use Serendipity\Presentation\Output;

final class Ok extends Output
{
    public function __construct(mixed $content, array $properties = [])
    {
        parent::__construct($content, $properties);
    }

    public static function createFrom(mixed $content = null, array $properties = []): Ok
    {
        if ($content instanceof Message) {
            return new self(
                $content->content(),
                array_merge($content->properties()->toArray(), $properties)
            );
        }
        if ($content instanceof Exportable) {
            return new self($content->export(), $properties);
        }
        return new self($content, $properties);
    }
}
