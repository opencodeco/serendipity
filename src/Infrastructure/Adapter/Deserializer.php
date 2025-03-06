<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter;

use InvalidArgumentException;
use Serendipity\Domain\Contract\Adapter\Deserializer as Contract;
use Serendipity\Domain\Contract\Formatter;
use Serendipity\Domain\Support\Reflective\CaseConvention;
use Serendipity\Infrastructure\Adapter\Deserialize\Demolisher;

use function is_object;

/**
 * @template T of object
 * @implements Contract<T>
 */
class Deserializer extends Demolisher implements Contract
{
    /**
     * @param class-string<T> $type
     * @param array<callable|Formatter> $formatters
     */
    public function __construct(
        public readonly string $type,
        CaseConvention $case = CaseConvention::SNAKE,
        array $formatters = [],
    ) {
        parent::__construct($case, $formatters);
    }

    /**
     * @param T $instance
     * @return array<string, mixed>
     */
    public function deserialize(mixed $instance): array
    {
        if (is_object($instance) && $instance::class !== $this->type) {
            throw new InvalidArgumentException('Invalid instance type');
        }

        return $this->demolish($instance);
    }
}
