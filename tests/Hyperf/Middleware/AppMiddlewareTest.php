<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Middleware;

use FastRoute\Dispatcher;
use Hyperf\Context\ResponseContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\Handler;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Contract\Message;
use Serendipity\Domain\Support\Set;
use Serendipity\Hyperf\Middleware\AppMiddleware;
use Serendipity\Presentation\Output;
use Swow\Psr7\Message\ResponsePlusInterface;
use Swow\Psr7\Message\ServerRequestPlusInterface;

/**
 * @internal
 */
final class AppMiddlewareTest extends TestCase
{
    public function testShouldRenderOutputResponse(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $class) => $this->createMock($class));
        $middleware = new AppMiddleware($container);

        $request = $this->createMock(ServerRequestPlusInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponsePlusInterface::class);

        ResponseContext::set($response);

        $properties = [
            'Invalid-Property' => 1,
            'Custom-Property' => 'CustomValue',
        ];
        $output = new Output(null, $properties);

        $request->method('getAttribute')
            ->willReturn(
                new Dispatched([
                    Dispatcher::FOUND,
                    new Handler(fn () => $output, ''),
                    [],
                ])
            );

        $response->expects($this->once())
            ->method('addHeader')
            ->with('content-type', 'application/json')
            ->willReturnSelf();

        $middleware->process($request, $handler);
    }

    public function testShouldRenderWithoutOutput(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $class) => $this->createMock($class));
        $middleware = new AppMiddleware($container);

        $request = $this->createMock(ServerRequestPlusInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponsePlusInterface::class);

        ResponseContext::set($response);

        $request->expects($this->once())
            ->method('getAttribute')
            ->willReturn(
                new Dispatched([
                    Dispatcher::FOUND,
                    new Handler(fn () => null, ''),
                    [],
                ])
            );

        $middleware->process($request, $handler);
    }

    #[TestWith([null])]
    #[TestWith([204])]
    #[TestWith([300])]
    #[TestWith([400])]
    #[TestWith([500])]
    public function testShouldRenderByStatus(?int $statusCode): void
    {
        $config = $this->createMock(ConfigInterface::class);
        $config->expects($this->once())
            ->method('get')
            ->willReturn($statusCode);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $class) => match ($class) {
                ConfigInterface::class => $config,
                default => $this->createMock($class),
            });
        $middleware = new AppMiddleware($container);

        $request = $this->createMock(ServerRequestPlusInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponsePlusInterface::class);

        ResponseContext::set($response);

        $message = $this->createMock(Message::class);
        $message->expects($this->once())
            ->method('properties')
            ->willReturn(
                Set::createFrom([
                    'Invalid-Property' => 1,
                    'Custom-Property' => 'CustomValue',
                ])
            );
        $request->method('getAttribute')
            ->willReturn(
                new Dispatched([
                    Dispatcher::FOUND,
                    new Handler(fn () => $message, ''),
                    [],
                ])
            );

        $response->expects($this->once())
            ->method('addHeader')
            ->with('content-type', 'application/json')
            ->willReturnSelf();

        $middleware->process($request, $handler);
    }

    public function testShouldRenderExportableResponse(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(fn (string $class) => $this->createMock($class));
        $middleware = new AppMiddleware($container);

        $request = $this->createMock(ServerRequestPlusInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponsePlusInterface::class);

        ResponseContext::set($response);

        $exportable = $this->createMock(Exportable::class);
        $exportable->method('export')
            ->willReturn(['key' => 'value']);

        $request->method('getAttribute')
            ->willReturn(
                new Dispatched([
                    Dispatcher::FOUND,
                    new Handler(fn () => $exportable, ''),
                    [],
                ])
            );

        $response->expects($this->once())
            ->method('addHeader')
            ->with('content-type', 'application/json')
            ->willReturnSelf();

        $middleware->process($request, $handler);
    }
}
