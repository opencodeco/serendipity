<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Collection;

use DomainException;
use PHPUnit\Framework\TestCase;
use Serendipity\Test\Domain\Collection\CollectionTestMock as Collection;
use Serendipity\Test\Domain\Collection\CollectionTestMockStub as Stub;
use stdClass;

/**
 * @internal
 */
final class CollectionTest extends TestCase
{
    public function testShouldCreateFromArray(): void
    {
        $collection = new Collection();
        $collection->push(new Stub('foo'));
        $collection->push(new Stub('bar'));

        $this->assertCount(2, $collection);
    }

    public function testShouldFailOnInvalidDatum(): void
    {
        $this->expectException(DomainException::class);

        $collection = new Collection();
        $collection->push(new stdClass());
    }
}
