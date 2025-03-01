<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Entity;

use Serendipity\Domain\Entity\Entity;
use PHPUnit\Framework\TestCase;

final class EntityTest extends TestCase
{
    public function testShouldExposeValues(): void
    {
        $entity = new class extends Entity {
            protected string $value = 'none';
        };

        $this->assertEquals(['value' => 'none'], $entity->expose()->toArray());
    }
}
