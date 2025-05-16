<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Serendipity\Hyperf\Event\HttpHandleCompleted;
use Serendipity\Hyperf\Event\HttpHandleInterrupted;
use Serendipity\Hyperf\Event\HttpHandleStarted;
use Serendipity\Hyperf\Listener\SentryHttpListener;
use Serendipity\Infrastructure\Exception\Additional;
use Serendipity\Infrastructure\Exception\AdditionalFactory;
use Serendipity\Infrastructure\Exception\Thrown;
use stdClass;
use Throwable;

class SentryHttpListenerTest extends TestCase
{
    private ConfigInterface|MockObject $config;
    private LoggerInterface|MockObject $logger;
    private AdditionalFactory|MockObject $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = $this->createMock(ConfigInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->factory = $this->createMock(AdditionalFactory::class);
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
        $listener = new SentryHttpListener($this->config, $this->logger, $this->factory);

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

        $listener = new SentryHttpListener($this->config, $this->logger, $this->factory);

        // Act
        $result = $listener->listen();

        // Assert
        $this->assertEmpty($result);
    }

    public function testListenReturnsHttpEventsWhenDsnIsSet(): void
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

        $listener = new SentryHttpListener($this->config, $this->logger, $this->factory);

        // Act
        $result = $listener->listen();

        // Assert
        $this->assertEquals([
            HttpHandleStarted::class,
            HttpHandleInterrupted::class,
            HttpHandleCompleted::class,
        ], $result);
    }

    public function testProcessCallsSentryInitOnHttpHandleStarted(): void
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

        $listener = new SentryHttpListener($this->config, $this->logger, $this->factory);
        $request = $this->createMock(RequestInterface::class);
        $event = new HttpHandleStarted($request);

        // Act
        $listener->process($event);
    }

    public function testProcessCapturesExceptionOnHttpHandleInterrupted(): void
    {
        // Arrange
        $options = [
            'dsn' => null,
            'environment' => 'testing',
        ];

        $this->config->expects($this->once())
            ->method('get')
            ->with('sentry')
            ->willReturn($options);

        $request = $this->createMock(RequestInterface::class);
        $exception = $this->createMock(Throwable::class);

        // Create a mock for Thrown to use in Additional constructor
        $thrown = $this->createMock(Thrown::class);
        $thrown->method('context')->willReturn(['thrown_context' => 'value']);
        $thrown->method('resume')->willReturn('Error message');

        // Create a real Additional object with the constructor
        $additional = new Additional(
            line: 'GET /test',
            body: ['test' => 'value'],
            headers: ['Content-Type' => 'application/json'],
            query: ['q' => 'test'],
            message: 'Error message',
            thrown: $thrown,
            errors: []
        );

        $context = $additional->context();

        $this->factory->expects($this->once())
            ->method('make')
            ->with($request, $exception)
            ->willReturn($additional);

        $this->logger->expects($this->once())
            ->method('debug')
            ->with('Sentry captured exception', $context);

        $listener = new SentryHttpListener($this->config, $this->logger, $this->factory);
        $event = new HttpHandleInterrupted($request, $exception);

        // Act
        $listener->process($event);
    }

    public function testProcessCallsFallbackForUnsupportedEvent(): void
    {
        // Arrange
        $this->logger->expects($this->once())
            ->method('warning')
            ->with(
                'Sentry integration does not support this event',
                ['event' => 'stdClass']
            );

        $listener = new SentryHttpListener($this->config, $this->logger, $this->factory);

        // Act
        $listener->process(new stdClass());
    }
}
