<?php

declare(strict_types=1);

namespace Serendipity\Test\Infrastructure\Database\Document;

use Hyperf\Contract\ConfigInterface;
use PHPUnit\Framework\TestCase;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Serendipity\Infrastructure\Database\Document\SleekDBFactory;

/**
 * @internal
 */
final class SleekDBDatabaseFactoryTest extends TestCase
{
    use MakeExtension;

    public function testShouldCreateStore(): void
    {
        $config = $this->make(ConfigInterface::class);
        $options = $config->get('databases.sleek');
        $factory = new SleekDBFactory($options);
        $store = $factory->make('resource');
        $this->assertEquals('resource', $store->getStoreName());
    }
}
