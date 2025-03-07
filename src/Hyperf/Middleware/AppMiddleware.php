<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Middleware;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\CoreMiddleware as Hyperf;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Serendipity\Domain\Contract\Message;
use Serendipity\Infrastructure\Http\JsonFormatter;
use Swow\Psr7\Message\ResponsePlusInterface;

use function is_string;
use function Serendipity\Type\Cast\integerify;
use function sprintf;

class AppMiddleware extends Hyperf
{
    private readonly ConfigInterface $config;

    private readonly JsonFormatter $formatter;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container, 'http');

        $this->config = $container->get(ConfigInterface::class);
        $this->formatter = $container->get(JsonFormatter::class);
    }

    protected function handleFound(Dispatched $dispatched, ServerRequestInterface $request): mixed
    {
        $response = parent::handleFound($dispatched, $request);
        if (! $response instanceof Message) {
            return $response;
        }

        $statusCode = $this->normalizeStatusCode($response);

        $output = $this->response()
            ->addHeader('content-type', 'application/json')
            ->withStatus($statusCode);

        $output = $this->configureHeaders($response, $output);

        if ($statusCode === 204) {
            return $output->setBody(new SwooleStream());
        }

        $body = $response->content();
        $contents = $this->formatter->format($body);
        return $output->setBody(new SwooleStream($contents));
    }

    private function normalizeStatusCode(Message $response): int
    {
        $statusCode = integerify($this->config->get(sprintf('http.result.%s.status', $response::class)));
        if ($statusCode === 0) {
            return 200;
        }
        return $statusCode;
    }

    private function configureHeaders(Message $response, ResponsePlusInterface $output): ResponsePlusInterface
    {
        $properties = $response->properties()->toArray();
        foreach ($properties as $key => $value) {
            if (! is_string($value)) {
                continue;
            }
            $output = $output->withAddedHeader(sprintf('X-%s', $key), $value);
        }
        return $output;
    }
}
