<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Entity\Entity;

/**
 * @internal
 */
final class EntityTest extends TestCase
{
    public function testShouldExposeValues(): void
    {
        $entity = new class extends Entity {
            protected string $value = 'none';
        };

        $this->assertEquals(['value' => 'none'], $entity->export()->toArray());
    }
}
