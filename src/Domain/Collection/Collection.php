<?php

declare(strict_types=1);

namespace Serendipity\Domain\Collection;

use DomainException;

/**
 * @template T
 * @extends AbstractCollection<T>
 */
abstract class Collection extends AbstractCollection
{
    final public function __construct()
    {
        parent::__construct([]);
    }

    final public function push(mixed $datum): void
    {
        $this->data[] = $this->validate($datum);
    }

    abstract protected function validate(mixed $datum): mixed;

    protected function exception(string $type, mixed $datum): DomainException
    {
        $message = sprintf('Invalid type. Expected "%s", got "%s"', $type, get_debug_type($datum));
        return new DomainException($message);
    }
}
