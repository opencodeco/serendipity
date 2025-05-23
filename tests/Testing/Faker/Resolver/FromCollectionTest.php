<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Faker\Resolver;

use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use Serendipity\Domain\Support\Set;
use Serendipity\Example\Game\Domain\Collection\Game\FeatureCollection;
use Serendipity\Testing\Faker\Resolver\FromCollection;
use stdClass;

final class FromCollectionTest extends TestCase
{
    public function testShouldResolveSuccessfully(): void
    {
        // Arrange
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('isBuiltin')
            ->willReturn(false);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(FeatureCollection::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);

        $fromCollection = new FromCollection();
        $presets = Set::createFrom([]);

        // Act
        $result = $fromCollection->resolve($parameter, $presets);

        // Assert
        $this->assertIsArray($result->content);
        $this->assertNotEmpty($result->content);
    }


    public function testShouldNotResolveCollectionWhenParameterIsNotCollection(): void
    {
        // Arrange
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('isBuiltin')
            ->willReturn(false);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(stdClass::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $set = Set::createFrom([]);
        $fromCollection = new FromCollection();

        // Act
        $result = $fromCollection->resolve($parameter, $set);

        // Assert
        $this->assertNull($result);
    }
}
