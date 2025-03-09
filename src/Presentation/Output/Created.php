<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Output;

use Serendipity\Presentation\Output;

/**
 * The request succeeded and a new resource was created.
 * This is usually the response after POST or PUT requests.
 */
final class Created extends Output
{
    public function __construct(string $id)
    {
        parent::__construct(content: $id, properties: ['id' => $id]);
    }

    public static function createFrom(string $id): Created
    {
        return new self($id);
    }
}
