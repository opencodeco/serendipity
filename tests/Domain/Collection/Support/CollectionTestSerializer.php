<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Collection\Support;

use DomainException;
use Serendipity\Domain\Contract\Serializer;

class CollectionTestSerializer implements Serializer
{
    public function serialize(array $datum): CollectionTestMockStub
    {
        if (! isset($datum['value'])) {
            throw new DomainException('Invalid data. Datum must have a "value" key');
        }
        return new CollectionTestMockStub($datum['value']);
    }
}
