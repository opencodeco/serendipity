<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Testing\Extension;

use PHPUnit\Framework\Constraint\IsTrue;
use Serendipity\Hyperf\Testing\Observability\LogRecord;
use Serendipity\Hyperf\Testing\Observability\MemoryLoggerStore;

use function Serendipity\Type\Json\encode;

/**
 * @phpstan-ignore trait.unused
 */
trait LoggerExtension
{
    private bool $isLoggerSetup = false;

    abstract public static function fail(string $message = ''): never;

    protected function setUpLogger(): void
    {
        $this->registerTearDown(fn () => $this->tearDownLogger(false));
    }

    /**
     * @SuppressWarnings(StaticAccess)
     */
    protected function tearDownLogger(bool $flag): void
    {
        $this->isLoggerSetup = $flag;
        MemoryLoggerStore::clear();
    }

    /**
     * @SuppressWarnings(StaticAccess)
     */
    protected function assertLogged(?string $pattern = null, ?string $level = null): void
    {
        if (! $this->isLoggerSetup) {
            static::fail('Request is not set up.');
        }

        $where = fn (LogRecord $record) => ($pattern === null || preg_match($pattern, $record->message))
            && ($level === null || $record->level === $level);
        $tallied = MemoryLoggerStore::tally($where);
        $filters = [
            'pattern' => $pattern,
            'level' => $level,
        ];
        $message = sprintf(
            "Expected to find at least one item in logs with level '%s' using '%s'",
            $level,
            encode($filters),
        );
        static::assertThat($tallied > 0, new IsTrue(), $message);
    }

    abstract protected function registerTearDown(callable $callback): void;
}
