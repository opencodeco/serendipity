<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serializing\Deserialize\Resolve;

use Serendipity\Infrastructure\Adapter\Serializing\Deserialize\Resolve\DoNothingChain;
use Serendipity\Test\TestCase;

class DoNothingChainTest extends TestCase
{
    final public function testResolveValue(): void
    {
        $chain = new DoNothingChain();
        $value = 'test';
        $result = $chain->resolve($value);

        $this->assertEquals('test', $result->content);
    }
}
