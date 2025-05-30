<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Adapter\Serialize\Resolver;

use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Exception\Adapter\NotResolved;
use Serendipity\Domain\Support\Set;
use Serendipity\Example\Game\Domain\Collection\Game\FeatureCollection;
use Serendipity\Example\Game\Domain\Entity\Game\Feature;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Infrastructure\Adapter\Serialize\Resolver\CollectionValue;
use Serendipity\Testing\Extension\FakerExtension;
use stdClass;

final class CollectionValueTest extends TestCase
{
    use MakeExtension;
    use FakerExtension;

    public function testShouldResolveSuccessfully(): void
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
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn('features');

        $faker = $this->faker();
        $set = Set::createFrom([
            'features' => [
                $faker->fake(Feature::class)->toArray(),
                $faker->fake(Feature::class)->toArray(),
            ],
        ]);
        $collectionValue = new CollectionValue();

        // Act
        $result = $collectionValue->resolve($parameter, $set);

        // Assert
        $this->assertInstanceOf(FeatureCollection::class, $result->content);
        $this->assertCount(2, $result->content);
    }

    public function testShouldResolveWhenTheValueIsNotValid(): void
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
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn('features');

        $set = Set::createFrom([
            'features' => 0,
        ]);
        $collectionValue = new CollectionValue();

        // Act
        $result = $collectionValue->resolve($parameter, $set);

        // Assert
        $this->assertInstanceOf(FeatureCollection::class, $result->content);
        $this->assertCount(0, $result->content);
    }

    public function testShouldNotResolveCollectionWhenParameterIsNotCollection(): void
    {
        // Arrange
        $chain = new CollectionValue();
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(stdClass::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $set = Set::createFrom([]);

        // Act
        $result = $chain->resolve($parameter, $set);

        // Assert
        $this->assertInstanceOf(NotResolved::class, $result->content);
    }
}
