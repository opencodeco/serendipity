<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface;
use Serendipity\Example\Game\Domain\Repository\GameCommandRepository;
use Serendipity\Example\Game\Domain\Repository\GameQueryRepository;
use Serendipity\Example\Game\Infrastructure\Repository\SleekDB\SleekDBGameCommandRepository;
use Serendipity\Example\Game\Infrastructure\Repository\SleekDB\SleekDBGameQueryRepository;
use Serendipity\Hyperf\Database\Document\HyperfSleekDBFactory;
use Serendipity\Hyperf\Database\Relational\HyperfConnectionFactory;
use Serendipity\Hyperf\Logging\EnvironmentLoggerFactory;
use Serendipity\Infrastructure\Database\Document\SleekDBFactory;
use Serendipity\Infrastructure\Database\Relational\ConnectionFactory;

use function Hyperf\Support\env;
use function Serendipity\Type\Cast\toString;

return [
    LoggerInterface::class => fn (Container $container) => $container
        ->get(EnvironmentLoggerFactory::class)
        ->make(toString(env('APP_ENV', 'dev'))),

    SleekDBFactory::class => fn (Container $container) => (new HyperfSleekDBFactory())->make($container),
    ConnectionFactory::class => HyperfConnectionFactory::class,

    GameCommandRepository::class => SleekDBGameCommandRepository::class,
    GameQueryRepository::class => SleekDBGameQueryRepository::class,
];
