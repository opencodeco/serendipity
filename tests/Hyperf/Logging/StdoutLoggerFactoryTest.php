<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Logging;

use Hyperf\Contract\ConfigInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Serendipity\Hyperf\Logging\StdoutLoggerFactory;
use Serendipity\Infrastructure\Logging\StdoutLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @internal
 */
final class StdoutLoggerFactoryTest extends TestCase
{
    private ContainerInterface $container;
    private ConfigInterface $config;
    private ConsoleOutput $consoleOutput;
    private StdoutLoggerFactory $stdoutLoggerFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->createMock(ContainerInterface::class);
        $this->config = $this->createMock(ConfigInterface::class);
        $this->consoleOutput = $this->createMock(ConsoleOutput::class);

        $this->stdoutLoggerFactory = new StdoutLoggerFactory(
            container: $this->container
        );
    }

    public function testMakeShouldReturnStdoutLoggerWithDefaultConfig(): void
    {
        // Arrange
        $defaultLevels = [
            'alert',
            'critical',
            'emergency',
            'error',
            'warning',
            'notice',
            'info',
            'debug',
        ];

        $this->config->expects($this->once())
            ->method('get')
            ->with('logger.default.levels', $this->anything())
            ->willReturn($defaultLevels);

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [ConsoleOutput::class, $this->consoleOutput],
                [ConfigInterface::class, $this->config],
            ]);

        // Act
        $logger = $this->stdoutLoggerFactory->make('test-env');

        // Assert
        $this->assertInstanceOf(StdoutLogger::class, $logger);

        // Verificar propriedades usando Reflection
        $reflection = new ReflectionClass($logger);

        $outputProperty = $reflection->getProperty('output');
        $outputProperty->setAccessible(true);
        $this->assertSame($this->consoleOutput, $outputProperty->getValue($logger));

        $levelsProperty = $reflection->getProperty('levels');
        $levelsProperty->setAccessible(true);
        $this->assertSame($defaultLevels, $levelsProperty->getValue($logger));

        $envProperty = $reflection->getProperty('env');
        $envProperty->setAccessible(true);
        $this->assertSame('test-env', $envProperty->getValue($logger));
    }

    public function testMakeShouldReturnStdoutLoggerWithCustomConfig(): void
    {
        // Arrange
        $customLevels = [
            'error',
            'critical',
        ];

        $this->config->expects($this->once())
            ->method('get')
            ->with('logger.default.levels', $this->anything())
            ->willReturn($customLevels);

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [ConsoleOutput::class, $this->consoleOutput],
                [ConfigInterface::class, $this->config],
            ]);

        // Act
        $logger = $this->stdoutLoggerFactory->make('production');

        // Assert
        $this->assertInstanceOf(StdoutLogger::class, $logger);

        // Verificar propriedades usando Reflection
        $reflection = new ReflectionClass($logger);

        $levelsProperty = $reflection->getProperty('levels');
        $levelsProperty->setAccessible(true);
        $this->assertSame($customLevels, $levelsProperty->getValue($logger));

        $envProperty = $reflection->getProperty('env');
        $envProperty->setAccessible(true);
        $this->assertSame('production', $envProperty->getValue($logger));
    }

    public function testMakeShouldHandleNullLevelsConfig(): void
    {
        // Arrange
        $defaultLevels = [
            'alert',
            'critical',
            'emergency',
            'error',
            'warning',
            'notice',
            'info',
            'debug',
        ];

        $this->config->expects($this->once())
            ->method('get')
            ->with(
                'logger.default.levels',
                $this->callback(function ($default) use ($defaultLevels) {
                    return $default === $defaultLevels;
                })
            )
            ->willReturn(null);

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [ConsoleOutput::class, $this->consoleOutput],
                [ConfigInterface::class, $this->config],
            ]);

        // Act
        $logger = $this->stdoutLoggerFactory->make('dev');

        // Assert
        $this->assertInstanceOf(StdoutLogger::class, $logger);

        // Verificar se o arrayify lidou com o valor nulo corretamente
        $reflection = new ReflectionClass($logger);
        $levelsProperty = $reflection->getProperty('levels');
        $levelsProperty->setAccessible(true);
        $this->assertIsArray($levelsProperty->getValue($logger));
    }
}
