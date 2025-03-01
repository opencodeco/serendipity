<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Exception;

use DateTimeImmutable;
use Hyperf\Contract\ConfigInterface;
use Throwable;

class ThrownFactory
{
    public function __construct(private readonly ConfigInterface $config)
    {
    }

    public function make(Throwable $throwable, DateTimeImmutable $at = new DateTimeImmutable()): Thrown
    {
        return new Thrown(
            $this->type($throwable::class),
            $at,
            $throwable::class,
            $throwable->getMessage(),
            $throwable->getCode(),
            $throwable->getFile(),
            $throwable->getLine(),
            $throwable->getTraceAsString(),
            $this->previous($throwable->getPrevious()),
        );
    }

    private function previous(?Throwable $getPrevious): ?Thrown
    {
        if ($getPrevious === null) {
            return null;
        }
        return $this->make($getPrevious);
    }

    private function type(string $throwable): Type
    {
        $type = $this->config->get(sprintf('exception.classification.%s', $throwable));
        if ($type instanceof Type) {
            return $type;
        }
        return match ($type) {
            Type::INVALID_INPUT->value => Type::INVALID_INPUT,
            default => Type::UNTREATED,
        };
    }
}
