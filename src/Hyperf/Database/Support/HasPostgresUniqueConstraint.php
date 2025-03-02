<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Database\Support;

use Serendipity\Domain\Exception\UniqueKeyViolationException;
use Throwable;

use function Serendipity\Type\Cast\toString;

trait HasPostgresUniqueConstraint
{
    protected function detectUniqueKeyViolation(Throwable $exception): ?UniqueKeyViolationException
    {
        $message = $exception->getMessage();
        $pattern = '/duplicate key value violates unique constraint\s+?"([^"]+)"\s+.*\(([^)]+)\)=\(([^)]+)\).*/m';
        if (! preg_match($pattern, $message, $matches)) {
            return null;
        }
        $resource = toString($matches[1]);
        $key = toString($matches[2]);
        $value = toString($matches[3]);
        return new UniqueKeyViolationException($key, $value, $resource, $exception);
    }
}
