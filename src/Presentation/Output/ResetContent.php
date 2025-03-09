<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

/**
 * The server fulfilled the request but asked the user to reset the document.
 * For example, if a user submits a contact form, the server might respond with a 205 to signal that the form should
 * be cleared and ready for new input.
 */
final class ResetContent extends Success
{
}
