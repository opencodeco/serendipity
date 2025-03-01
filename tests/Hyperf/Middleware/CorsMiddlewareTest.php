<?php

declare(strict_types=1);

namespace Serendipity\Test\Hyperf\Middleware;

use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Serendipity\Hyperf\Middleware\CorsMiddleware;

/**
 * @internal
 */
final class CorsMiddlewareTest extends TestCase
{
    public function testShouldAllowCors(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('withHeader')
            ->willReturnSelf();
        Context::set(ResponseInterface::class, $response);

        $config = $this->createMock(ConfigInterface::class);
        $config->expects($this->once())
            ->method('get')
            ->with('cors.allow_origin', '*')
            ->willReturn('*');
        $middleware = new CorsMiddleware($config);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request);

        $middleware->process($request, $handler);
    }

    public function testShouldAllowCorsWhenResponseIsNotInstanceOfResponseInterface(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('withHeader')
            ->willReturnSelf();
        Context::set(ResponseInterface::class, $response);

        $config = $this->createMock(ConfigInterface::class);
        $config->expects($this->once())
            ->method('get')
            ->with('cors.allow_origin', '*')
            ->willReturn('*');
        $middleware = new CorsMiddleware($config);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('OPTIONS');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())
            ->method('handle');

        $middleware->process($request, $handler);
    }
}
