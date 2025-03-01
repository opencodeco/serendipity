<?php

declare(strict_types=1);

namespace Serendipity\Testing;

use Faker\Generator;
use FastRoute\Dispatcher;
use Hyperf\Context\Context;
use Hyperf\HttpMessage\Server\Request;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\Handler;
use Psr\Http\Message\ServerRequestInterface;
use Serendipity\Infrastructure\Adapter\Serialize\Builder;
use Serendipity\Testing\Faker\Faker;

use function Hyperf\Support\make;

/**
 * @phpstan-ignore trait.unused
 */
trait HelperFactory
{
    protected ?Faker $faker = null;

    protected ?Builder $builder = null;

    protected function faker(): Faker
    {
        if ($this->faker === null) {
            $this->faker = make(Faker::class);
        }
        return $this->faker;
    }

    protected function generator(): Generator
    {
        return $this->faker()->engine;
    }

    protected function builder(): Builder
    {
        if ($this->builder === null) {
            $this->builder = make(Builder::class);
        }
        return $this->builder;
    }

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
        array $headers = []
    ): mixed {
        $this->makeRequestContext($parsedBody, $queryParams, $params, $headers);
        return $this->make($class);
    }

    /**
     * @SuppressWarnings(StaticAccess)
     */
    final protected function makeRequestContext(
        array $parsedBody = [],
        array $queryParams = [],
        array $params = [],
        array $headers = [],
    ): void {
        $array = [
            Dispatcher::FOUND,
            new Handler(fn () => null, ''),
            $params,
        ];
        $value = (new Request('POST', ''))
            ->withParsedBody($parsedBody)
            ->withQueryParams($queryParams)
            ->withAttribute(Dispatched::class, new Dispatched($array))
            ->withHeaders($headers);
        Context::set(ServerRequestInterface::class, $value);
    }
}
