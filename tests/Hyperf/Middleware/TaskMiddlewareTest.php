<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Middleware;

use Hyperf\Contract\ConfigInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Serendipity\Domain\Support\Task;
use Serendipity\Hyperf\Middleware\TaskMiddleware;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Testing\Extension\FakerExtension;

final class TaskMiddlewareTest extends TestCase
{
    use MakeExtension;
    use FakerExtension;

    public function testShouldExtractFromHeader(): void
    {
        // Arrange
        $task = $this->make(Task::class);
        $correlationId = $this->generator()->uuid();
        $platformId = $this->generator()->uuid();

        $config = $this->createMock(ConfigInterface::class);
        $config->method('get')
            ->willReturnCallback(fn ($key) => match ($key) {
                'task.default.correlation_id' => ['X-Correlation-ID', 'header'],
                'task.default.platform_id' => ['X-Platform-ID', 'header'],
                default => null,
            });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $id) => match ($id) {
                Task::class => $task,
                ConfigInterface::class => $config,
                default => null,
            });

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getHeaderLine')
            ->willReturnCallback(fn (string $name) => match ($name) {
                'X-Correlation-ID' => $correlationId,
                'X-Platform-ID' => $platformId,
                default => '',
            });

        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($response);

        // Act
        $middleware = new TaskMiddleware($container);
        $middleware->process($request, $handler);

        // Assert
        $this->assertEquals($correlationId, $task->getCorrelationId());
        $this->assertEquals($platformId, $task->getPlatformId());
    }

    public function testShouldExtractFromQuery(): void
    {
        // Arrange
        $task = $this->make(Task::class);
        $correlationId = $this->generator()->uuid();
        $platformId = $this->generator()->uuid();

        $config = $this->createMock(ConfigInterface::class);
        $config->method('get')
            ->willReturnCallback(fn ($key) => match ($key) {
                'task.default.correlation_id' => ['correlation_id', 'query'],
                'task.default.platform_id' => ['platform_id', 'query'],
                default => null,
            });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $id) => match ($id) {
                Task::class => $task,
                ConfigInterface::class => $config,
                default => null,
            });

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')
            ->willReturn([
                'correlation_id' => $correlationId,
                'platform_id' => $platformId,
            ]);

        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($response);

        // Act
        $middleware = new TaskMiddleware($container);
        $middleware->process($request, $handler);

        // Assert
        $this->assertEquals($correlationId, $task->getCorrelationId());
        $this->assertEquals($platformId, $task->getPlatformId());
    }

    public function testShouldExtractFromCookie(): void
    {
        // Arrange
        $task = $this->make(Task::class);
        $correlationId = $this->generator()->uuid();
        $platformId = $this->generator()->uuid();

        $config = $this->createMock(ConfigInterface::class);
        $config->method('get')
            ->willReturnCallback(fn ($key) => match ($key) {
                'task.default.correlation_id' => ['correlation_id', 'cookie'],
                'task.default.platform_id' => ['platform_id', 'cookie'],
                default => null,
            });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $id) => match ($id) {
                Task::class => $task,
                ConfigInterface::class => $config,
                default => null,
            });

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getCookieParams')
            ->willReturn([
                'correlation_id' => $correlationId,
                'platform_id' => $platformId,
            ]);

        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($response);

        // Act
        $middleware = new TaskMiddleware($container);
        $middleware->process($request, $handler);

        // Assert
        $this->assertEquals($correlationId, $task->getCorrelationId());
        $this->assertEquals($platformId, $task->getPlatformId());
    }

    public function testShouldExtractFromParsedBody(): void
    {
        // Arrange
        $task = $this->make(Task::class);
        $correlationId = $this->generator()->uuid();
        $platformId = $this->generator()->uuid();

        $config = $this->createMock(ConfigInterface::class);
        $config->method('get')
            ->willReturnCallback(fn ($key) => match ($key) {
                'task.default.correlation_id' => ['correlation_id', 'body'],
                'task.default.platform_id' => ['platform_id', 'body'],
                default => null,
            });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $id) => match ($id) {
                Task::class => $task,
                ConfigInterface::class => $config,
                default => null,
            });

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')
            ->willReturn([
                'correlation_id' => $correlationId,
                'platform_id' => $platformId,
            ]);

        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($response);

        // Act
        $middleware = new TaskMiddleware($container);
        $middleware->process($request, $handler);

        // Assert
        $this->assertEquals($correlationId, $task->getCorrelationId());
        $this->assertEquals($platformId, $task->getPlatformId());
    }

    public function testShouldNotExtractWhenTypeIsUnknown(): void
    {
        // Arrange
        $task = $this->make(Task::class);

        $config = $this->createMock(ConfigInterface::class);
        $config->method('get')
            ->willReturnCallback(fn ($key) => match ($key) {
                'task.default.correlation_id' => ['correlation_id', '***'],
                'task.default.platform_id' => ['platform_id', '***'],
                default => null,
            });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $id) => match ($id) {
                Task::class => $task,
                ConfigInterface::class => $config,
                default => null,
            });

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($response);

        // Act
        $middleware = new TaskMiddleware($container);
        $middleware->process($request, $handler);

        // Assert
        $this->assertEquals('N/A', $task->getCorrelationId());
        $this->assertEquals('N/A', $task->getPlatformId());
    }

    public function testShouldUseNotApplicableCorrelationIdWhenNotPresent(): void
    {
        // Arrange
        $task = $this->make(Task::class);
        $platformId = $this->generator()->uuid();

        $config = $this->createMock(ConfigInterface::class);
        $config->method('get')
            ->willReturnCallback(fn ($key) => match ($key) {
                'task.default.correlation_id' => ['X-Correlation-ID', 'header'],
                'task.default.platform_id' => ['X-Platform-ID', 'header'],
                default => null,
            });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $id) => match ($id) {
                Task::class => $task,
                ConfigInterface::class => $config,
                default => null,
            });

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getHeaderLine')
            ->willReturnCallback(fn (string $name) => match ($name) {
                'X-Correlation-ID' => '',
                'X-Platform-ID' => $platformId,
                default => '',
            });

        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($response);

        // Act
        $middleware = new TaskMiddleware($container);
        $middleware->process($request, $handler);

        // Assert
        $this->assertNotEmpty($task->getCorrelationId());
        $this->assertEquals($platformId, $task->getPlatformId());
    }

    public function testShouldUseNotApplicablePlatformIdWhenNotPresent(): void
    {
        // Arrange
        $task = $this->make(Task::class);
        $correlationId = $this->generator()->uuid();

        $config = $this->createMock(ConfigInterface::class);
        $config->method('get')
            ->willReturnCallback(fn ($key) => match ($key) {
                'task.default.correlation_id' => ['X-Correlation-ID', 'header'],
                'task.default.platform_id' => ['X-Platform-ID', 'header'],
                default => null,
            });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $id) => match ($id) {
                Task::class => $task,
                ConfigInterface::class => $config,
                default => null,
            });

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getHeaderLine')
            ->willReturnCallback(fn (string $name) => match ($name) {
                'X-Correlation-ID' => $correlationId,
                default => '',
            });

        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($response);

        // Act
        $middleware = new TaskMiddleware($container);
        $middleware->process($request, $handler);

        // Assert
        $this->assertEquals($correlationId, $task->getCorrelationId());
        $this->assertNotEmpty($task->getPlatformId());
    }

    public function testShouldHandleExceptionDuringExtraction(): void
    {
        // Arrange
        $task = $this->make(Task::class);

        $config = $this->createMock(ConfigInterface::class);
        $config->method('get')
            ->willReturnCallback(fn ($key) => match ($key) {
                'task.default.correlation_id' => ['X-Correlation-ID', 'header'],
                'task.default.platform_id' => ['X-Platform-ID', 'header'],
                default => null,
            });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $id) => match ($id) {
                Task::class => $task,
                ConfigInterface::class => $config,
                default => null,
            });

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getHeaderLine')
            ->willThrowException(new RuntimeException('Header extraction failed'));

        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($response);

        // Act
        $middleware = new TaskMiddleware($container);
        $middleware->process($request, $handler);

        // Assert
        $this->assertEquals('ERR', $task->getCorrelationId());
        $this->assertEquals('ERR', $task->getPlatformId());
    }
}
