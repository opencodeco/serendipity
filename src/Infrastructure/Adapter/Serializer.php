<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Adapter;

use Serendipity\Domain\Contract\Adapter\Serializer as Contract;
use Serendipity\Domain\Support\Values;
use Serendipity\Infrastructure\Adapter\Serialize\Builder;
use Serendipity\Infrastructure\CaseConvention;

/**
 * @template T of object
 * @implements Contract<T>
 */
class Serializer extends Builder implements Contract
{
    /**
     * @param class-string<T> $type
     */
    public function __construct(
        public readonly string $type,
        CaseConvention $case = CaseConvention::SNAKE,
        array $formatters = [],
    ) {
        parent::__construct($case, $formatters);
    }

    /**
     * @return T
     */
    public function serialize(array $datum): mixed
    {
        return $this->build($this->type, Values::createFrom($datum));
    }
}
