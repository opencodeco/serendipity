<?php

declare(strict_types=1);

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Serendipity\Example\Game\Domain\Repository\GameCommandRepository;
use Serendipity\Example\Game\Domain\Repository\GameQueryRepository;
use Serendipity\Example\Game\Infrastructure\Repository\SleekDB\SleekDBGameCommandRepository;
use Serendipity\Example\Game\Infrastructure\Repository\SleekDB\SleekDBGameQueryRepository;
use Serendipity\Hyperf\Database\Relational\HyperfConnectionFactory;
use Serendipity\Hyperf\Logging\EnvironmentLoggerFactory;
use Serendipity\Infrastructure\Database\Document\SleekDBFactory;
use Serendipity\Infrastructure\Database\Relational\ConnectionFactory;

use function Hyperf\Support\env;
use function Serendipity\Type\Cast\toArray;
use function Serendipity\Type\Cast\toString;

return [
    LoggerInterface::class => fn (ContainerInterface $container) => $container
        ->get(EnvironmentLoggerFactory::class)
        ->make(toString(env('APP_ENV', 'dev'))),
    SleekDBFactory::class => function (ContainerInterface $container) {
        $config = $container->get(ConfigInterface::class);
        $options = toArray($config->get('databases.sleek'));
        return new SleekDBFactory($options);
    },
    ConnectionFactory::class => HyperfConnectionFactory::class,

    GameCommandRepository::class => SleekDBGameCommandRepository::class,
    GameQueryRepository::class => SleekDBGameQueryRepository::class,
];
