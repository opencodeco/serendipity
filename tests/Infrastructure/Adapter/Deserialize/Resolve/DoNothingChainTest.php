<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize\Resolve;

use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DoNothingChain;

final class DoNothingChainTest extends TestCase
{
    public function testResolveValue(): void
    {
        $chain = new DoNothingChain();
        $value = 'test';
        $result = $chain->resolve($this->createMock(ReflectionParameter::class), $value);

        $this->assertEquals('test', $result->content);
    }
}
