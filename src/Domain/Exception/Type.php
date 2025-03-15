<?php

declare(strict_types=1);

namespace Serendipity\Domain\Exception;

enum Type: string
{
    case UNTREATED = 'untreated';
    case INVALID_INPUT = 'invalid_input';
}
