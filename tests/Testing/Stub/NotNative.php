<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

use Serendipity\Test\Testing\Stub\Type\BackedEnumeration;
use Serendipity\Test\Testing\Stub\Type\Enumeration;

class NotNative
{
    public function __construct(
        public readonly BackedEnumeration $backed,
        public readonly Enumeration $enum,
        public readonly Stub $stub,
    ) {
    }
}
