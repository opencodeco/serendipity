<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

use Countable;
use Iterator;

class Variety
{
    public function __construct(
        public readonly int|string $union,
        public readonly Countable&Iterator $intersection,
        public readonly EntityStub $nested,
        private readonly mixed $whatever,
    ) {
    }

    public function getWhatever()
    {
        return $this->whatever;
    }
}
