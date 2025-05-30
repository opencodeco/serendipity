<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Deserialize\Resolve;

use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Example\Game\Domain\Collection\Game\FeatureCollection;
use Serendipity\Example\Game\Domain\Collection\GameCollection;
use Serendipity\Infrastructure\Adapter\Deserialize\Resolve\CollectionChain;
use stdClass;

final class CollectionChainTest extends TestCase
{
    public function testShouldResolveCollectionSuccessfully(): void
    {
        // Arrange
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(FeatureCollection::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);

        $chain = new CollectionChain();
        $collection = new FeatureCollection();

        // Act
        $result = $chain->resolve($parameter, $collection);

        // Assert
        $this->assertEquals([], $result->content);
    }

    public function testShouldNotResolveCollectionWhenParameterIsNotCollection(): void
    {
        // Arrange
        $chain = new CollectionChain();
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(stdClass::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $collection = new FeatureCollection();

        // Act
        $result = $chain->resolve($parameter, $collection);

        // Assert
        $this->assertEquals($collection, $result->content);
    }

    public function testShouldNotResolveCollectionWhenParameterTypeNotMatch(): void
    {
        // Arrange
        $chain = new CollectionChain();
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(GameCollection::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $collection = new FeatureCollection();

        // Act
        $result = $chain->resolve($parameter, $collection);

        // Assert
        $this->assertEquals($collection, $result->content);
    }
}
