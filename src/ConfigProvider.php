<?php

declare(strict_types=1);

namespace Serendipity;

use Hyperf\HttpServer\CoreMiddleware;
use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler;
use Hyperf\Validation\Middleware\ValidationMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Infrastructure\Http\Exception\Handler\AppExceptionHandler;
use Serendipity\Infrastructure\Http\Exception\Handler\ValidationExceptionHandler;
use Serendipity\Infrastructure\Http\Middleware\AppMiddleware;
use Serendipity\Infrastructure\Logging\EnvironmentLoggerFactory;
use Serendipity\Presentation\Output\Accepted;
use Serendipity\Presentation\Output\Created;
use Serendipity\Presentation\Output\NoContent;
use Serendipity\Presentation\Output\NotFound;

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
            'http' => [
                'result' => [
                    Created::class => [
                        'status' => 201,
                    ],
                    Accepted::class => [
                        'status' => 202,
                    ],
                    NoContent::class => [
                        'status' => 204,
                    ],
                    NotFound::class => [
                        'status' => 404,
                    ],
                ],
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
