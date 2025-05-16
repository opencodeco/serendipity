<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Framework\Event\MainWorkerStart;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Serendipity\Hyperf\Listener\SentryHttpListener;
use Throwable;

class SentryInitializeListenerTest extends TestCase
{
    private ConfigInterface|MockObject $config;
    private LoggerInterface|MockObject $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = $this->createMock(ConfigInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testConstructorInitializesOptions(): void
    {
        // Arrange
        $sentryConfig = [
            'dsn' => 'https://example.com/sentry',
            'environment' => 'testing',
        ];

        $this->config->expects($this->once())
            ->method('get')
            ->with('sentry')
            ->willReturn($sentryConfig);

        // Act
        $listener = new SentryHttpListener($this->config, $this->logger);

        // Assert - We can't directly test private properties, but we can test the behavior
        $this->assertNotEmpty($listener->listen());
    }

    public function testListenReturnsEmptyArrayWhenDsnIsNotSet(): void
    {
        // Arrange
        $sentryConfig = [
            'environment' => 'testing',
        ];

        $this->config->expects($this->once())
            ->method('get')
            ->with('sentry')
            ->willReturn($sentryConfig);

        $listener = new SentryHttpListener($this->config, $this->logger);

        // Act
        $result = $listener->listen();

        // Assert
        $this->assertEmpty($result);
    }

    public function testListenReturnsMainWorkerStartWhenDsnIsSet(): void
    {
        // Arrange
        $sentryConfig = [
            'dsn' => 'https://example.com/sentry',
            'environment' => 'testing',
        ];

        $this->config->expects($this->once())
            ->method('get')
            ->with('sentry')
            ->willReturn($sentryConfig);

        $listener = new SentryHttpListener($this->config, $this->logger);

        // Act
        $result = $listener->listen();

        // Assert
        $this->assertEquals([MainWorkerStart::class], $result);
    }

    public function testProcessCallsSentryInit(): void
    {
        // Arrange
        $options = [
            'dsn' => 'https://example.com/sentry',
            'environment' => 'testing',
        ];

        $this->config->expects($this->once())
            ->method('get')
            ->with('sentry')
            ->willReturn($options);

        $callback = fn (array $context
        ) => $context['exception'] instanceof Throwable && $context['options'] === $options;
        $this->logger->expects($this->once())
            ->method('emergency')
            ->with('Sentry initialization failed', $this->callback($callback));

        $listener = new SentryHttpListener($this->config, $this->logger);
        $event = $this->createMock(MainWorkerStart::class);

        // Act
        $listener->process($event);
    }
}
