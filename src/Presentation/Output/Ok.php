<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

use Serendipity\Domain\Contract\Exportable;
use Serendipity\Domain\Contract\Message;
use Serendipity\Presentation\Output;

final class Ok extends Output
{
    public function __construct(?array $values, array $properties = [])
    {
        parent::__construct($properties, $values);
    }

    public static function createFrom(array|Message|Exportable $data = null, array $properties = []): Ok
    {
        if (is_array($data) || $data === null) {
            return new self($data, $properties);
        }
        if ($data instanceof Message) {
            return new self($data->content()->toArray(), array_merge($data->properties()->toArray(), $properties));
        }
        if ($data instanceof Exportable) {
            return new self($data->export(), $properties);
        }
        return new self(null, $properties);
    }
}
