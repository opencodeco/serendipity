<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

use Serendipity\Presentation\Output;

use function sprintf;

final class NotFound extends Output
{
    public function __construct(string $missing, int|string $what)
    {
        $properties = [
            'Missing' => sprintf('"%s" identified by "%s" not found', $missing, $what),
        ];
        parent::__construct($properties);
    }

    public static function createFrom(string $missing, int|string $what): NotFound
    {
        return new self($missing, $what);
    }
}
