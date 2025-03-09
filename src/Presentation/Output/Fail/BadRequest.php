<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output\Fail;

/**
 * The server can’t or won’t process the request due to a client error. For example, invalid request message framing,
 * deceptive request routing, size too large, etc.
 */
final class BadRequest extends Fail
{
}
