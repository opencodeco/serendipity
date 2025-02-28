<?php

declare(strict_types=1);

namespace Serendipity;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Infrastructure\Logging\EnvironmentLoggerFactory;

use function Hyperf\Support\env;
use function Serendipity\Type\Cast\toString;

class ConfigProvider
{
    /**
     * @SuppressWarnings(ExcessiveMethodLength)
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                LoggerInterface::class => static fn (ContainerInterface $container) => $container
                    ->get(EnvironmentLoggerFactory::class)
                    ->make(toString(env('APP_ENV', 'dev'))),
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
