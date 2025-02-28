<?php

declare(strict_types=1);

namespace Serendipity\Infrastructure\Persistence\Converter;

use Serendipity\Infrastructure\Adapter\Serializing\Converter;

use function is_array;
use function is_string;
use function Serendipity\Type\Json\encode;

class FromArrayToDatabase implements Converter
{
    public function convert(mixed $value): ?string
    {
        if (is_string($value)) {
            return $value;
        }
        if (! is_array($value)) {
            return null;
        }
        return encode($value);
    }
}
