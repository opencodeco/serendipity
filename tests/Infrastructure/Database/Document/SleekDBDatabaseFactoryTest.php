<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Database\Document;

use Hyperf\Contract\ConfigInterface;
use Serendipity\Hyperf\Testing\CanMake;
use Serendipity\Infrastructure\Database\Document\SleekDBDatabaseFactory;
use PHPUnit\Framework\TestCase;

final class SleekDBDatabaseFactoryTest extends TestCase
{
    use CanMake;

    public function testShouldCreateStore(): void
    {
        $config = $this->make(ConfigInterface::class);
        $options = $config->get('databases.sleek');
        $factory = new SleekDBDatabaseFactory($options);
        $store = $factory->make('resource');
        $this->assertEquals('resource', $store->getStoreName());
    }
}
