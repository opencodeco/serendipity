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
use ReflectionException;
use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Contract\Message;
use Serendipity\Infrastructure\Adapter\Deserialize\Demolisher;
use Serendipity\Infrastructure\Http\JsonFormatter;
use Serendipity\Infrastructure\Http\ResponseType;
use Swow\Psr7\Message\ResponsePlusInterface;

use function is_string;
use function Serendipity\Type\Cast\integerify;
use function sprintf;

class AppMiddleware extends Hyperf
{
    private readonly ConfigInterface $config;

    private readonly JsonFormatter $formatter;

    private readonly Demolisher $demolisher;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container, 'http');

        $this->config = $container->get(ConfigInterface::class);
        $this->formatter = $container->get(JsonFormatter::class);
        $this->demolisher = $container->get(Demolisher::class);
    }

    /**
     * @throws ReflectionException
     */
    protected function handleFound(Dispatched $dispatched, ServerRequestInterface $request): mixed
    {
        $response = parent::handleFound($dispatched, $request);
        return match (true) {
            $response instanceof Message => $this->handleFoundMessage($response),
            $response instanceof Exportable => $this->handleFoundExportable($response),
            default => $response,
        };
    }

    private function handleFoundMessage(Message $message): ResponsePlusInterface
    {
        $statusCode = $this->detectStatusCode($message);

        $response = $this->response()
            ->addHeader('content-type', 'application/json')
            ->withStatus($statusCode);

        $response = $this->configureHeaders($message, $response);

        if ($statusCode === 204) {
            return $response->setBody(new SwooleStream());
        }

        $value = $message->content();
        $option = $this->detectType($statusCode);
        $contents = $this->formatter->format($value, $option);
        return $response->setBody(new SwooleStream($contents));
    }

    /**
     * @throws ReflectionException
     */
    private function handleFoundExportable(Exportable $exportable): ResponsePlusInterface
    {
        $value = $this->demolisher->demolish($exportable);
        $contents = $this->formatter->format($value);
        return $this->response()
            ->addHeader('content-type', 'application/json')
            ->withStatus(200)
            ->setBody(new SwooleStream($contents));
    }

    private function detectStatusCode(Message $response): int
    {
        $statusCode = integerify($this->config->get(sprintf('http.result.%s.status', $response::class)));
        if ($statusCode === 0) {
            return 200;
        }
        return $statusCode;
    }

    private function configureHeaders(Message $message, ResponsePlusInterface $response): ResponsePlusInterface
    {
        $properties = $message->properties()->toArray();
        foreach ($properties as $key => $value) {
            if (! is_string($value)) {
                continue;
            }
            $response = $response->withAddedHeader(sprintf('X-%s', $key), $value);
        }
        return $response;
    }

    private function detectType(int $statusCode): ?ResponseType
    {
        return match (true) {
            $statusCode >= 200 && $statusCode < 300 => ResponseType::SUCCESS,
            $statusCode >= 400 && $statusCode < 500 => ResponseType::FAIL,
            $statusCode >= 500 => ResponseType::ERROR,
            default => null,
        };
    }
}
