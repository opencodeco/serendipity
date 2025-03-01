<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize\Resolve;

use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\DependencyChain;
use stdClass;

/**
 * @internal
 */
final class DependencyChainTest extends TestCase
{
    final public function testResolveObject(): void
    {
        $chain = new DependencyChain();
        $object = new stdClass();
        $result = $chain->resolve($object);

        $this->assertIsArray($result->content);
    }

    final public function testResolveNonObject(): void
    {
        $chain = new DependencyChain();
        $value = 'test';
        $result = $chain->resolve($value);

        $this->assertEquals('test', $result->content);
    }
}
