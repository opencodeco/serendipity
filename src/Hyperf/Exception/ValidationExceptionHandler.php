<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Domain\Exception\InvalidInputException;
use Serendipity\Domain\Exception\Type;
use Serendipity\Infrastructure\Http\JsonFormatter;
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

        $errors = $this->extractErrors($throwable);

        $message = sprintf('<validation> %s', $throwable->getMessage());
        $this->logger->notice($message, $errors);

        return $response
            ->setStatus($this->extractStatus($throwable))
            ->addHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(new SwooleStream($this->formatter->format($errors, Type::INVALID_INPUT)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException || $throwable instanceof InvalidInputException;
    }

    private function extractErrors(Throwable $throwable): array
    {
        return match (true) {
            $throwable instanceof ValidationException => $throwable->validator->errors()->getMessages(),
            $throwable instanceof InvalidInputException => $throwable->getErrors(),
            default => [],
        };
    }

    private function extractStatus(Throwable $throwable): int
    {
        return match (true) {
            $throwable instanceof ValidationException => $throwable->status,
            $throwable instanceof InvalidInputException => 428,
            default => 400,
        };
    }
}
