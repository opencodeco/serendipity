<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize\Resolve;

use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DoNothingChain;
use PHPUnit\Framework\TestCase;

final class DoNothingChainTest extends TestCase
{
    final public function testResolveValue(): void
    {
        $chain = new DoNothingChain();
        $value = 'test';
        $result = $chain->resolve($value);

        $this->assertEquals('test', $result->content);
    }
}
