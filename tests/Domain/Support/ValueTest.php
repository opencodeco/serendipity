<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Support;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Value;

/**
 * @internal
 */
final class ValueTest extends TestCase
{
    public function testShouldHaveContent(): void
    {
        $value = new Value('foo');
        $this->assertEquals('foo', $value->content);
    }
}
