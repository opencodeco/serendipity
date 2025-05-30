<?php

declare(strict_types=1);

namespace Serendipity\Domain\Collection;

use DomainException;
use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Support\Datum;

/**
 * @template T
 * @extends AbstractCollection<T>
 */
abstract class Collection extends AbstractCollection implements Exportable
{
    protected bool $unsafe = false;

    final public function __construct()
    {
        parent::__construct([]);
    }

    public function export(): array
    {
        return $this->data;
    }

    final public function push(object $datum): void
    {
        if ($this->unsafe && $datum instanceof Datum) {
            $this->data[] = $datum;
            return;
        }
        $this->data[] = $this->validate($datum);
    }

    abstract protected function validate(mixed $datum): mixed;

    protected function exception(string $type, mixed $datum): DomainException
    {
        $message = sprintf('Invalid type. Expected "%s", got "%s"', $type, get_debug_type($datum));
        return new DomainException($message);
    }
}
