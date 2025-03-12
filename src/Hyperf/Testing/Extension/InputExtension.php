<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing\Extension;

use FastRoute\Dispatcher;
use Hyperf\Context\Context;
use Hyperf\HttpMessage\Server\Request;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\Handler;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @phpstan-ignore trait.unused
 */
trait InputExtension
{
    private bool $isRequestSetUp = false;

    abstract public static function fail(string $message = ''): never;

    /**
     * @SuppressWarnings(StaticAccess)
     */
    protected function setUpInput(): void
    {
        $this->tearDownInput(true);
        $this->registerTearDown(fn () => $this->tearDownInput(false));
    }

    /**
     * @SuppressWarnings(StaticAccess)
     */
    protected function tearDownInput(bool $isRequestSetUp): void
    {
        $this->isRequestSetUp = $isRequestSetUp;
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
        array $args = [],
    ): mixed {
        if ($this->isRequestSetUp) {
            $this->setUpRequestContext($parsedBody, $queryParams, $params, $headers);
            return $this->make($class, $args);
        }
        static::fail('Request is not set up.');
    }

    /**
     * @SuppressWarnings(StaticAccess)
     */
    final protected function setUpRequestContext(
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

    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     */
    abstract protected function make(string $class, array $args = []): mixed;

    abstract protected function registerTearDown(callable $callback): void;
}
