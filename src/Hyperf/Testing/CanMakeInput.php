<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing;

use FastRoute\Dispatcher;
use Hyperf\Context\Context;
use Hyperf\HttpMessage\Server\Request;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\Handler;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @phpstan-ignore trait.unused
 */
trait CanMakeInput
{
    /**
     * @SuppressWarnings(StaticAccess)
     */
    protected function tearDownRequest(): void
    {
        Context::destroy(ServerRequestInterface::class);
        Context::destroy('http.request.parsedData');
    }

    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @return T
     */
    final protected function input(
        string $class,
        array $parsedBody = [],
        array $queryParams = [],
        array $params = [],
        array $headers = [],
        string $method = 'POST',
        string $uri = '/',
    ): mixed {
        $this->setUpRequest($parsedBody, $queryParams, $params, $headers, $method, $uri);
        return $this->make($class);
    }

    /**
     * @SuppressWarnings(StaticAccess)
     */
    final protected function setUpRequest(
        array $parsedBody = [],
        array $queryParams = [],
        array $params = [],
        array $headers = [],
        string $method = 'POST',
        string $uri = '/',
    ): void {
        $array = [
            Dispatcher::FOUND,
            new Handler(fn () => null, ''),
            $params,
        ];
        $value = (new Request($method, $uri))
            ->withParsedBody($parsedBody)
            ->withQueryParams($queryParams)
            ->withAttribute(Dispatched::class, new Dispatched($array))
            ->withHeaders($headers);
        Context::set(ServerRequestInterface::class, $value);
    }

    abstract protected function make(string $class, array $args = []): mixed;
}
