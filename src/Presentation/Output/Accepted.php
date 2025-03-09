<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

use Serendipity\Presentation\Output;

/**
 * The request was accepted but is still in progress.
 * It’s used for cases where another server handles the request or for batch processing.
 */
final class Accepted extends Output
{
    public function __construct(int|string $content)
    {
        parent::__construct(content: $content, properties: ['token' => $content]);
    }

    public static function createFrom(string $trackingId): Accepted
    {
        return new self($trackingId);
    }
}
