<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Collection;

use DomainException;
use Exception;
use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Datum;
use Serendipity\Test\Domain\Collection\CollectionTestMock as Collection;
use Serendipity\Test\Domain\Collection\CollectionTestMockStub as Stub;
use stdClass;

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

    public function testShouldExportData(): void
    {
        $collection = new Collection();
        $stub1 = new Stub('foo');
        $stub2 = new Stub('bar');

        $collection->push($stub1);
        $collection->push($stub2);

        $exported = $collection->export();

        $this->assertIsArray($exported);
        $this->assertCount(2, $exported);
        $this->assertSame($stub1, $exported[0]);
        $this->assertSame($stub2, $exported[1]);
    }

    public function testShouldAllowNonStrictMode(): void
    {
        // Arrange
        $collection = (new Collection())->setStrict(true);
        $this->expectException(DomainException::class);

        // Act
        $collection->push(new Datum([], new Exception()));

        // Assert
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(stdClass::class, $collection->current());
    }
}
