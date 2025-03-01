<?php

declare(strict_types=1);

namespace Serendipity\Domain\Exception\Adapter;

enum Type
{
    case INVALID;
    case REQUIRED;
}
