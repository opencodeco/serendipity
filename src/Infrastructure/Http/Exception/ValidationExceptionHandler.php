<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Http\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Infrastructure\Exception\Type;
use Serendipity\Infrastructure\Http\Formatter\JsonFormatter;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly JsonFormatter $formatter,
    ) {
    }

    public function handle(Throwable $throwable, ResponsePlusInterface $response): MessageInterface|ResponseInterface
    {
        $this->stopPropagation();

        /** @var ValidationException $throwable */
        $body = $throwable->validator->errors()->getMessages();

        $this->logger->info($throwable->getMessage(), $body);

        return $response
            ->setStatus($throwable->status)
            ->addHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(new SwooleStream($this->formatter->format($body, Type::INVALID_INPUT)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
