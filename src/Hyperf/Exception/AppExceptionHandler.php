<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Infrastructure\Exception\ThrownFactory;
use Serendipity\Infrastructure\Exception\Type;
use Serendipity\Infrastructure\Http\JsonFormatter;
use Throwable;

use function array_map;
use function in_array;
use function Serendipity\Type\Cast\toInt;
use function Serendipity\Type\Cast\toString;
use function sprintf;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var array<string>
     */
    private array $ignored = [];

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ThrownFactory $factory,
        private readonly JsonFormatter $formatter,
    ) {
    }

    public function handle(Throwable $throwable, ResponseInterface $response): MessageInterface|ResponseInterface
    {
        $thrown = $this->factory->make($throwable);

        $message = sprintf('[AppExceptionHandler] %s', $thrown->resume());
        $context = $thrown->context();

        match ($thrown->type) {
            Type::INVALID_INPUT => $this->logger->notice($message, $context),
            default => $this->logger->alert($message, $context),
        };

        $code = $this->code($throwable);
        $contents = $this->formatter->format($context, $thrown->type);
        return $response->withStatus($code)
            ->withBody(new SwooleStream($contents));
    }

    public function isValid(Throwable $throwable): bool
    {
        $haystack = array_map(fn (mixed $candidate) => toString($candidate), $this->ignored);
        return ! in_array($throwable::class, $haystack, true);
    }

    public function code(Throwable $throwable): int
    {
        $code = toInt($throwable->getCode());
        return ($code < 400 || $code > 599) ? 500 : $code;
    }
}
