<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use Serendipity\Domain\Exception\InvalidInputException;
use Serendipity\Infrastructure\Exception\Thrown;
use Serendipity\Infrastructure\Exception\ThrownFactory;
use Serendipity\Infrastructure\Http\JsonFormatter;
use Throwable;

use function array_map;
use function implode;
use function is_array;
use function Serendipity\Type\Cast\stringify;

abstract class AbstractExceptionHandler extends ExceptionHandler
{
    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly JsonFormatter $formatter,
        protected readonly ThrownFactory $factory,
        protected readonly RequestInterface $request,
    ) {
    }

    protected function extractContext(Throwable $throwable, ?Thrown $thrown = null): array
    {
        $thrown ??= $this->factory->make($throwable);

        $errors = match (true) {
            $throwable instanceof ValidationException => $throwable->validator->errors()->getMessages(),
            $throwable instanceof InvalidInputException => $throwable->getErrors(),
            default => [],
        };
        return [
            'message' => $thrown->resume(),
            'thrown' => $thrown->context(),
            'body' => $this->request->post(),
            'errors' => $errors,
            'headers' => $this->headers(),
            'query' => $this->request->query(),
            'request' => $this->request->getMethod() . ' ' . $this->request->getUri(),
        ];
    }

    protected function headers(): array
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
