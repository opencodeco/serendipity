<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Exception;

enum Type: string
{
    case UNTREATED = 'untreated';
    case INVALID_INPUT = 'invalid_input';
}
