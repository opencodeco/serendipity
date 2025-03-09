<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output\Fail;

use Serendipity\Presentation\Output;

/**
 * The server cannot produce a response matching the list of acceptable values defined in the request's headers.
 * This typically relates to content negotiation when the client specifies requirements the server cannot meet.
 */
final class NotAcceptable extends Output
{
    public function __construct(int|string $content)
    {
        parent::__construct(content: $content, properties: ['token' => $content]);
    }

    public static function createFrom(string $trackingId): NotAcceptable
    {
        return new self($trackingId);
    }
}
