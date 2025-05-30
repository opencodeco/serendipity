<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Collection;

use Serendipity\Domain\Collection\Collection;
use Serendipity\Test\Domain\Collection\CollectionTestMockStub as Stub;

final class CollectionTestMock extends Collection
{
    protected bool $strict = false;

    public function setStrict(bool $strict): self
    {
        $this->strict = $strict;
        return $this;
    }

    public function current(): Stub
    {
        return $this->validate($this->datum());
    }

    protected function validate(mixed $datum): Stub
    {
        if ($datum instanceof Stub) {
            return $datum;
        }
        throw $this->exception(Stub::class, $datum);
    }
}
