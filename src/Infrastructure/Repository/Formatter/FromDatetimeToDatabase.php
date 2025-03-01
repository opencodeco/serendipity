<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Repository\Formatter;

use DateTimeInterface;
use Serendipity\Domain\Contract\Formatter;

use function is_string;

class FromDatetimeToDatabase implements Formatter
{
    public function format(mixed $value, mixed $option = null): ?string
    {
        if (is_string($value)) {
            return $value;
        }
        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }
        return null;
    }
}
