<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Exception;

use Exception;
use Hyperf\Contract\ConfigInterface;
use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Exception\ThrownFactory;
use Serendipity\Infrastructure\Exception\Type;

final class ThrownFactoryTest extends TestCase
{
    public function testShouldMakeThrown(): void
    {
        // Arrange
        $config = $this->createMock(ConfigInterface::class);
        $factory = new ThrownFactory($config);
        $throwable = new Exception('_|_');

        // Act
        $config
            ->expects($this->once())
            ->method('get')
            ->willReturn(null);
        $thrown = $factory->make($throwable);

        // Assert
        $this->assertEquals($throwable->getMessage(), $thrown->message);
    }

    public function testShouldMakeThrownWithTypeFromConfig(): void
    {
        // Arrange
        $config = $this->createMock(ConfigInterface::class);
        $factory = new ThrownFactory($config);
        $throwable = new Exception();

        // Act
        $config
            ->expects($this->once())
            ->method('get')
            ->willReturn(Type::INVALID_INPUT);
        $thrown = $factory->make($throwable);

        // Assert
        $this->assertEquals(Type::INVALID_INPUT, $thrown->type);
    }

    public function testShouldMakeThrownWithPrevious(): void
    {
        // Arrange
        $config = $this->createMock(ConfigInterface::class);
        $factory = new ThrownFactory($config);
        $throwable = new Exception(
            message: '1',
            previous: new Exception(
                message: '2',
                previous: new Exception('3')
            )
        );

        // Act
        $thrown = $factory->make($throwable);

        // Assert
        $context = $thrown->context();
        $this->assertEquals('1', $context['message']);
        $this->assertEquals('2', $context['previous']['message']);
        $this->assertEquals('3', $context['previous']['previous']['message']);
    }
}
