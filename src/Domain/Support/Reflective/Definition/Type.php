<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective\Definition;

enum Type: string
{
    case IP_V4 = 'ipv4';
    case IP_V6 = 'ipv6';
    case EMAIL = 'email';
    case FIRST_NAME = 'firstName';
    case LAST_NAME = 'lastName';
}
