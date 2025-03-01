<?php

declare(strict_types=1);

namespace Serendipity\Domain\Exception\Adapter;

enum NotResolvedType
{
    case INVALID;
    case REQUIRED;
}
