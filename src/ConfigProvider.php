<?php

declare(strict_types=1);

namespace Serendipity;

use Serendipity\Hyperf\Database\Document\HyperfSleekDBFactory;
use Serendipity\Hyperf\Database\Relational\HyperfConnectionFactory;
use Serendipity\Infrastructure\Database\Document\SleekDBFactory;
use Serendipity\Infrastructure\Database\Relational\ConnectionFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                SleekDBFactory::class => HyperfSleekDBFactory::class,
                ConnectionFactory::class => HyperfConnectionFactory::class,
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
