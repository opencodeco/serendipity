<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing\Observability;

use Hyperf\Collection\Collection;

final class MemoryLoggerStore
{
    private static ?Collection $collection = null;

    public static function add(string $level, string $message, array $context = []): void
    {
        self::collection()->push(new LogRecord($level, $message, $context));
    }

    public static function clear(): void
    {
        self::$collection = new Collection();
    }

    public static function tally(callable $where): int
    {
        return self::collection()
            ->where($where)
            ->count();
    }

    private static function collection(): Collection
    {
        self::$collection ??= new Collection();
        return self::$collection;
    }
}
