<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Domain\Exception\InvalidInputException;
use Serendipity\Domain\Exception\Type;
use Serendipity\Infrastructure\Http\JsonFormatter;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

use function Serendipity\Type\Cast\stringify;
use function sprintf;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly JsonFormatter $formatter,
        private readonly RequestInterface $request,
    ) {
    }

    public function handle(Throwable $throwable, ResponsePlusInterface $response): MessageInterface|ResponseInterface
    {
        $this->stopPropagation();

        $message = sprintf('<validation> %s', $throwable->getMessage());
        $context = $this->extractContext($throwable);
        $this->logger->debug($message, $context);

        return $response
            ->setStatus($this->extractStatus($throwable))
            ->addHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(new SwooleStream($this->formatter->format($context, Type::INVALID_INPUT)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException || $throwable instanceof InvalidInputException;
    }

    private function extractContext(Throwable $throwable): array
    {
        $errors = match (true) {
            $throwable instanceof ValidationException => $throwable->validator->errors()->getMessages(),
            $throwable instanceof InvalidInputException => $throwable->getErrors(),
            default => [],
        };
        return [
            'errors' => $errors,
            'headers' => $this->headers(),
            'query' => $this->request->query(),
            'body' => $this->request->post(),
        ];
    }

    private function extractStatus(Throwable $throwable): int
    {
        return match (true) {
            $throwable instanceof ValidationException => $throwable->status,
            $throwable instanceof InvalidInputException => 428,
            default => 400,
        };
    }

    private function headers(): array
    {
        $callback = function (mixed $header): string {
            if (! is_array($header)) {
                return stringify($header);
            }
            return implode('; ', array_map(fn (mixed $value) => stringify($value), $header));
        };
        return array_map($callback, $this->request->getHeaders());
    }
}
