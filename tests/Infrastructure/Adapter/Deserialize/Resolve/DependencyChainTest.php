<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize\Resolve;

use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DependencyChain;
use stdClass;

/**
 * @internal
 */
final class DependencyChainTest extends TestCase
{
    public function testResolveObject(): void
    {
        $chain = new DependencyChain();
        $object = new stdClass();
        $result = $chain->resolve($this->createMock(ReflectionParameter::class), $object);

        $this->assertIsArray($result->content);
    }

    public function testResolveNonObject(): void
    {
        $chain = new DependencyChain();
        $value = 'test';
        $result = $chain->resolve($this->createMock(ReflectionParameter::class), $value);

        $this->assertEquals('test', $result->content);
    }
}
