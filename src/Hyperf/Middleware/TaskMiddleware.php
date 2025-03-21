<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Middleware;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Serendipity\Domain\Support\Task;
use Throwable;

use function Serendipity\Type\Cast\arrayify;
use function Serendipity\Type\Cast\stringify;

readonly class TaskMiddleware implements MiddlewareInterface
{
    private Task $task;

    private ConfigInterface $config;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(protected ContainerInterface $container)
    {
        $this->task = $container->get(Task::class);
        $this->config = $container->get(ConfigInterface::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->task->setCorrelationId($this->extractCorrelationId($request))
            ->setPlatformId($this->extractPlatformId($request));

        return $handler->handle($request);
    }

    private function extractCorrelationId(ServerRequestInterface $request): string
    {
        try {
            $location = $this->location('http.task.correlation_id', ['X-Correlation-ID', 'header']);
            return $this->extract($request, ...$location) ?: bin2hex(random_bytes(16));
        } catch (Throwable) {
            return 'N/A';
        }
    }

    private function extractPlatformId(ServerRequestInterface $request): string
    {
        try {
            $location = $this->location('http.task.platform_id', ['X-Platform-ID', 'header']);
            return $this->extract($request, ...$location) ?: '-';
        } catch (Throwable) {
            return 'N/A';
        }
    }

    /**
     * @return array<string>
     */
    private function location(string $key, array $default): array
    {
        $location = arrayify($this->config->get($key, $default));
        return array_map(fn ($item) => stringify($item), $location);
    }

    private function extract(ServerRequestInterface $request, string $name, string $type = ''): string
    {
        $extracted = match ($type) {
            'header' => $request->getHeaderLine($name),
            'query' => $request->getQueryParams()[$name] ?? '',
            'cookie' => $request->getCookieParams()[$name] ?? '',
            'body' => arrayify($request->getParsedBody())[$name] ?? '',
            default => '',
        };
        return stringify($extracted);
    }
}
