<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

use Serendipity\Infrastructure\Adapter\Output;

final class Created extends Output
{
    public function __construct(string $id)
    {
        parent::__construct(content: ['id' => $id]);
    }
}
