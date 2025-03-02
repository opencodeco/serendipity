<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

use Countable;
use Iterator;

class Intersection
{
    public function __construct(public readonly Iterator&Countable $intersected)
    {
    }
}
