<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

/**
 * This is the standard response for successful HTTP requests.
 * The actual meaning of the response depends on the request method used:
 *   - GET: Resource obtained and is in the message body
 *   - HEAD: Headers are included in the response
 *   - POST or PUT: Resource describing the result of the action sent is in the message body
 *   - TRACE: Message body contains the request message as received by the server
 */
final class Ok extends Success
{
}
