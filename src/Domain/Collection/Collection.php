<?php

declare(strict_types=1);

namespace Serendipity\Domain\Collection;

use DomainException;
use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Support\Datum;

use function Serendipity\Type\Cast\mapify;
use function Serendipity\Type\Json\encode;

/**
 * @template T
 * @extends AbstractCollection<T>
 */
abstract class Collection extends AbstractCollection implements Exportable
{
    protected bool $strict = true;

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
        if ($this->strict && $datum instanceof Datum) {
            $message = sprintf(
                "A mal formed entity was pushed, but the strict is active: %s",
                encode(mapify($datum->export()))
            );
            throw new DomainException($message);
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
