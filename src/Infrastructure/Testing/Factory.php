<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Testing;

use BackedEnum;
use FastRoute\Dispatcher;
use Hyperf\Context\Context;
use Hyperf\HttpMessage\Server\Request;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\Handler;
use Psr\Http\Message\ServerRequestInterface;

use function Hyperf\Support\make;

trait Factory
{
    /**
     * @SuppressWarnings(StaticAccess)
     */
    protected function collectGarbage(): void
    {
        gc_collect_cycles();
        Context::destroy(ServerRequestInterface::class);
        Context::destroy('http.request.parsedData');
    }

    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     */
    protected function make(string $class, array $args = []): mixed
    {
        return make($class, $args);
    }

    final protected function assertEnumValue(BackedEnum $enum, string $value): void
    {
        $this->assertEquals($enum->value, $value);
    }

    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @return T
     */
    final protected function input(string $class, array $data = [], array $queryParams = [], array $params = []): mixed
    {
        $this->configureRequestContext($data, $queryParams, $params);
        return $this->make($class);
    }

    /**
     * @SuppressWarnings(StaticAccess)
     */
    final protected function configureRequestContext(
        array $data = [],
        array $queryParams = [],
        array $params = []
    ): void {
        $array = [
            Dispatcher::FOUND,
            new Handler(fn () => null, ''),
            $params,
        ];
        $value = (new Request('POST', ''))
            ->withParsedBody($data)
            ->withQueryParams($queryParams)
            ->withAttribute(Dispatched::class, new Dispatched($array));
        Context::set(ServerRequestInterface::class, $value);
    }
}
