<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

use Serendipity\Presentation\Output;

/**
 * The request was successfully processed, but there is no content. The headers may be useful.
 */
final class NoContent extends Output
{
    public function __construct(array $properties = [])
    {
        parent::__construct(null, $properties);
    }

    public static function createFrom(array $properties = []): NoContent
    {
        return new self($properties);
    }
}
