<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Persistence;

use DateTimeImmutable;
use DateTimeInterface;
use Serendipity\Domain\Exception\GeneratingException;
use Throwable;
use Visus\Cuid2\Cuid2;

class Generator
{
    public function __construct(public readonly int $length = 10)
    {
    }

    /**
     * @throws GeneratingException
     */
    public function id(): string
    {
        try {
            return (new Cuid2($this->length))->toString();
        } catch (Throwable $exception) {
            throw new GeneratingException('id', $exception);
        }
    }

    public function now(): string
    {
        return (new DateTimeImmutable())->format(DateTimeInterface::ATOM);
    }
}
