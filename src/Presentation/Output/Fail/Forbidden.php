<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output\Fail;

/**
 * The client doesn’t have access rights to the content. For example, it may require a password. Unlike the 401 HTTP
 * error code, the server does know the client’s identity.
 */
final class Forbidden extends Fail
{
}
