<?php

declare(strict_types=1);

namespace Serendipity;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Serendipity\Hyperf\Database\HyperfDatabaseFactory;
use Serendipity\Infrastructure\Database\Document\SleekDBDatabaseFactory;
use Serendipity\Infrastructure\Database\Relational\RelationalDatabaseFactory;

use function Serendipity\Type\Cast\toArray;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                SleekDBDatabaseFactory::class => function (ContainerInterface $container) {
                    $config = $container->get(ConfigInterface::class);
                    $options = toArray($config->get('databases.sleek'));
                    return new SleekDBDatabaseFactory($options);
                },
                RelationalDatabaseFactory::class => HyperfDatabaseFactory::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}
