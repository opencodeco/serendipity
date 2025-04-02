<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Domain\Exception\ThrowableType;
use Serendipity\Infrastructure\Exception\ThrownFactory;
use Serendipity\Infrastructure\Http\JsonFormatter;
use Serendipity\Infrastructure\Http\ResponseType;
use Throwable;

use function array_map;
use function in_array;
use function Serendipity\Type\Cast\integerify;
use function Serendipity\Type\Cast\stringify;
use function Serendipity\Type\Json\decode;
use function sprintf;

class GeneralExceptionHandler extends ExceptionHandler
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

        $message = sprintf('<general> %s', $thrown->resume());
        $context = $thrown->context();

        match ($thrown->type) {
            ThrowableType::INVALID_INPUT => $this->logger->debug($message, $context),
            ThrowableType::FALLBACK_REQUIRED => $this->logger->info($message, $context),
            ThrowableType::RETRY_AVAILABLE => $this->logger->warning($message, $context),
            ThrowableType::UNRECOVERABLE => $this->logger->error($message, $context),
            default => $this->logger->alert($message, $context),
        };

        $code = $this->code($throwable);
        $type = $this->detectType($thrown->type);
        $value = $this->format($type, $thrown->resume());
        $contents = $this->formatter->format($value, $type);
        return $response->withStatus($code)
            ->withBody(new SwooleStream($contents));
    }

    public function isValid(Throwable $throwable): bool
    {
        $haystack = array_map(fn (mixed $candidate) => stringify($candidate), $this->ignored);
        return ! in_array($throwable::class, $haystack, true);
    }

    public function code(Throwable $throwable): int
    {
        $code = integerify($throwable->getCode());
        return ($code < 400 || $code > 599) ? 500 : $code;
    }

    private function detectType(ThrowableType $type): ResponseType
    {
        return match ($type) {
            ThrowableType::INVALID_INPUT,
            ThrowableType::FALLBACK_REQUIRED,
            ThrowableType::RETRY_AVAILABLE => ResponseType::FAIL,
            ThrowableType::UNRECOVERABLE,
            ThrowableType::UNTREATED => ResponseType::ERROR,
        };
    }

    private function format(ResponseType $type, string $message): string|array|null
    {
        $data = decode($message);
        return match ($type) {
            ResponseType::FAIL => $data ?? ['message' => $message],
            ResponseType::ERROR => $message,
            default => null,
        };
    }
}
