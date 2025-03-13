<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Logging;

use Stringable;
use Symfony\Component\Console\Output\OutputInterface;

use function Serendipity\Type\Cast\stringify;

class StdoutLogger extends AbstractLogger
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly array $levels,
        private readonly string $env,
    ) {
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        if (! in_array($level, $this->levels, true)) {
            return;
        }
        $this->output->writeln(
            sprintf('[%s.%s] %s: %s', $this->env, stringify($level), $message, $this->context($context))
        );
    }

    protected function context(array $context): string
    {
        $items = [];
        foreach ($context as $key => $value) {
            $items[] = sprintf("%s%s", $this->key($key), $this->value($value));
        }
        return sprintf("[%s]", implode(', ', $items));
    }

    private function key(int|string $key): string
    {
        return match (true) {
            is_string($key) => sprintf("'%s' => ", $key),
            default => '',
        };
    }

    private function value(mixed $value): string
    {
        return match (true) {
            is_string($value) => sprintf("'%s'", $value),
            is_scalar($value) => (string) $value,
            is_array($value) => $this->context($value),
            is_object($value) => json_encode($value),
            default => 'null',
        };
    }
}
