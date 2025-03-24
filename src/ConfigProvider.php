<?php

declare(strict_types=1);

namespace Serendipity;

use Serendipity\Hyperf\Command\GenerateRules;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [],
            'commands' => [
                GenerateRules::class,
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
