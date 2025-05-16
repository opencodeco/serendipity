<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Exception;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Serendipity\Domain\Exception\InvalidInputException;
use Serendipity\Infrastructure\Http\ResponseType;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

use function sprintf;

class ValidationExceptionHandler extends AbstractExceptionHandler
{
    public function handle(Throwable $throwable, ResponsePlusInterface $response): MessageInterface|ResponseInterface
    {
        $this->stopPropagation();

        $message = sprintf('<validation> %s', $throwable->getMessage());
        $context = $this->extractContext($throwable);
        $this->logger->debug($message, $context);

        return $response
            ->setStatus($this->extractStatus($throwable))
            ->addHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(new SwooleStream($this->formatter->format($context, ResponseType::FAIL)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException || $throwable instanceof InvalidInputException;
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
