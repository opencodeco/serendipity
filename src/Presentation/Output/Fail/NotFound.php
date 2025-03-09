<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output\Fail;

use Serendipity\Presentation\Output;

use function sprintf;

/**
 * The server cannot find the requested resource.
 * This is one of the most common HTTP errors and typically means the URL is mistyped or the resource has been moved
 * or deleted.
 */
final class NotFound extends Output
{
    public function __construct(string $missing, int|string $what)
    {
        $properties = [
            'Missing' => sprintf('"%s" identified by "%s" not found', $missing, $what),
        ];
        parent::__construct(null, $properties);
    }

    public static function createFrom(string $missing, int|string $what): NotFound
    {
        return new self($missing, $what);
    }
}
